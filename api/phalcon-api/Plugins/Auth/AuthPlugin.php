<?php
declare(strict_types=1);

/**
 * This file is based in the Vökuró project.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Plugins\Auth;

//use Vokuro\Models\RememberTokens;
use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\ModelException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Discoveryfy\Models\FailedLogins;
use Discoveryfy\Models\SecurityEvents;
use Discoveryfy\Models\Sessions;
use Discoveryfy\Models\SuccessLogins;
use Discoveryfy\Models\Users;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Phalcon\Api\Constants\JWTClaims;
use Phalcon\Api\Http\Request;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phalcon\Security;
use Phalcon\Security\Random;
use function Phalcon\Api\Core\envValue;

/**
 * Class AuthPlugin
 * Manages Authentication
 *
 * @see https://docs.phalcon.io/4.0/en/security
 * @see https://github.com/lcobucci/jwt/blob/3.3/README.md
 * @see https://tools.ietf.org/html/rfc7519
 * @package Phalcon\Api\Plugins\Auth
 * @property Sessions     $session
 * @property Users        $user
 * @property Cache        $cache
 * @property Security     $security
 * @property Config       $config
 * @property Request      $request
 */
class AuthPlugin extends Injectable
{
    private $session;
    private $user;

    /**
     * @return Sessions|null
     */
    public function getSession(): ?Sessions
    {
        return $this->session ?: null;
    }

    /**
     * @return Users|null
     */
    public function getUser(): ?Users
    {
        return $this->user ?: null;
    }

    public function createCSRFLogin(): string
    {
        $token = $this->security->getToken();
        $this->saveCSRF(CacheKeys::getLoginCSRFCacheKey($token));
        return $token;
    }

    public function createCSRFRegister(): string
    {
        $token = $this->security->getToken();
        $this->saveCSRF(CacheKeys::getRegisterCSRFCacheKey($token));
        return $token;
    }

    public function checkCSRFLogin(): bool
    {
        $this->checkCSRFHeader(CacheKeys::getLoginCSRFCacheKey($this->request->getHeader('X-CSRF-TOKEN')));
        return true;
    }

    public function checkCSRFRegister(): bool
    {
        $this->checkCSRFHeader(CacheKeys::getRegisterCSRFCacheKey($this->request->getHeader('X-CSRF-TOKEN')));
        return true;
    }

    private function saveCSRF(string $cache_key): void
    {
        if (true !== $this->cache->set($cache_key, null)) {
            throw new InternalServerErrorException('Problem saving token into cache');
        }
    }

    private function checkCSRFHeader(string $cache_key): void
    {
        if (!$this->request->hasHeader('X-CSRF-TOKEN')) {
            throw new UnauthorizedException('CSRF not provided');
        }
        if (!$this->cache->has($cache_key)) {
            throw new BadRequestException('Invalid CSRF token');
        }
        //Delete CSRF token?
    }

    /**
     * Checks the user credentials
     *
     * @param array $credentials
     *
     * @return Auth
     * @throws BadRequestException
     * @throws ModelException
     */
    public function check($credentials): self
    {
        // Check if the user exist
//        $this->user = Users::findFirstByUsername($credentials['username']);
        $this->user = Users::findFirst(
            [
                'conditions' => 'username = :username:',
                'bind'       => [
                    'username' => $credentials['username'],
                ],
                'bindTypes'  => [
                    'username' => \Phalcon\Db\Column::BIND_PARAM_STR,
                ],
//                'cache'      => [
//                    'key'      => CacheKeys::getModelCacheKey('user', $credentials['username']),
//                    'lifetime' => 84600,
//                ],
            ]
        );

        if (!$this->user) {
            $this->registerUserThrottling(null);
            throw new BadRequestException('Wrong email/password combination');
        }

        // Check the password
        if (!$this->security->checkHash($credentials['password'], $this->user->getPasswordHash())) {
            $this->registerUserThrottling($this->user);
            throw new BadRequestException('Wrong email/password combination');
        }

        // Check if the user was flagged
        $this->checkUserFlags();

        // Register the successful login
        (new SecurityEvents())->createLoginSuccessEvent($this->request, $this->user);

        return $this;
    }

    /**
     * Implements login throttling
     * Reduces the effectiveness of brute force attacks
     *
     * @param Users|null $user
     */
    private function registerUserThrottling(?Users $user)
    {
        (new SecurityEvents())->createLoginFailureEvent($this->request, $user);

        /** @var Config $config */
        $config = $this->getDI()->get('config');
        $since_dt = (new \Datetime('now', new \DateTimeZone($config->path('app.timezone', 'UTC'))))
            ->sub(new \DateInterval('PT6H'))
            ->format('Y-m-d H:i:s');
//        $since_ts = time() - (3600 * 6);

        $ip = $this->request->getClientAddress();

        $attempts = SecurityEvents::getNumLoginAttempts($ip, $since_dt);

        if (false !== $this->config->path('app.debug')) {
            switch ($attempts) {
                case 1:
                case 2:
                    // no delay
                    break;
                case 3:
                case 4:
                    sleep(2);
                    break;
                default:
                    sleep(4);
                    break;
            }
        }
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @throws ModelException
     */
    private function checkUserFlags()
    {
        if (true !== $this->user->get('enabled')) {
            throw new ModelException('The user is inactive');
        }
//        if ($user->active != 'Y') {
//            throw new ModelException('The user is inactive');
//        }
//        if ($user->banned != 'N') {
//            throw new ModelException('The user is banned');
//        }
//        if ($user->suspended != 'N') {
//            throw new ModelException('The user is suspended');
//        }
    }

    /**
     * Returns the string token
     *
     * @return string
     * @throws ModelException
     */
    public function createSessionToken(): string
    {
        //Create session
        $this->session = (new Sessions())
            ->set('id', (new Random())->uuid());

        if (isset($this->user)) {
            $this->session
                ->set('user_id', $this->user->get('id'))
                ->set('name', $this->user->get('username'));
        }

//        $data = ['id' => (new Random())->uuid()];
//        if (isset($this->user)) {
//            $data['user_id'] = $this->user->get('id');
//            $data['name'] = $this->user->get('username');
//        }
//        $this->session = (new Sessions())->assign($data);
        if (false === $this->session->save()) {
//            throw new InternalServerErrorException($this->session->getMessages());
            throw new InternalServerErrorException('Error creating session');
        }

        //Create JWT Token
        $signer  = new Sha512();
        $builder = new Builder();
        $token   = $builder
            ->issuedBy($this->getTokenIssuer()) //iss
            ->permittedFor($this->getTokenAudience()) //aud
            ->relatedTo($this->session->get('id'), true) //sub
            ->issuedAt($this->getTokenTimeIssuedAt())   //iat
            ->canOnlyBeUsedAfter($this->getTokenTimeNotBefore()) //nbf
            ->expiresAt($this->getTokenTimeExpiration()) //exp
//            ->identifiedBy($this->security->getToken(), false) //jti
            ->getToken($signer, new Key($this->getPrivateKey()));

        // Save Token in Cache
        if (true !== $this->cache->set(CacheKeys::getJWTCacheKey($token), null)) {
            throw new InternalServerErrorException('Problem saving token into cache');
        }

        return $token->__toString();
    }

    /**
     * Returns the ValidationData object for this record (JWT)
     *
     * @return ValidationData
     */
    private function getValidationData(): ValidationData
    {
        $validationData = new ValidationData();
        $validationData->setIssuer($this->getTokenIssuer());        //iss
        $validationData->setAudience($this->getTokenAudience());    //aud
        $validationData->setSubject($this->session->get('id'));     //sub
        $validationData->setCurrentTime(time() + 10);    //iat-nbf-exp
//        $validationData->setId();                                 //jti -(random, not validated)

        return $validationData;
    }

    /**
     * @param string $token_str
     * @return bool
     * @throws InternalServerErrorException
     */
    public function verifyToken(string $token_str): bool
    {
        $token = $this->getToken($token_str);

        // First lets check if the token is in redis -> Is this token really issued here?
        if (!$this->cache->has(CacheKeys::getJWTCacheKey($token))) {
            return false;
        }

        // Recover session, and validate the token against it
        $session_id = $token->getClaim(JWTClaims::CLAIM_SUBJECT);
        $this->session = Sessions::findFirst([
            'id = :session_id:',
            'bind'          => ['session_id' => $session_id],
            'bindTypes'     => ['session_id' => \Phalcon\Db\Column::BIND_PARAM_STR],
            'cache'         => [
                'key'       => CacheKeys::getModelCacheKey('session', $session_id),
//              'lifetime'  => 84600,
            ],
        ]);

        if (false === $this->session) {
            return false;
        }

        // Validate & Verify token
        if (false === $token->validate($this->getValidationData())) {
            return false;
        }
        if (false === $token->verify((new Sha512()), new Key($this->getPrivateKey()))) {
            return false;
        }

        return true;
    }

    public function loadUser()
    {
        if (!isset($this->session)) {
            throw new InternalServerErrorException('Undefined session');
        }
        if (!isset($this->user)) { //Manual relationship loading
            $this->user = Users::findFirst(['id' => $this->session->get('user_id')]);
        }
//        if (!$this->session->isRelationshipLoaded('users')) { //Phalcon relationship loading
//            $this->user = $this->session->getRelated('users');
//        }
        if (!$this->user) {
            throw new InternalServerErrorException('Invalid user_id from session');
        }
    }

    /**
     * Returns the JWT token object
     *
     * @param string $token
     *
     * @return Token
     */
    protected function getToken(string $token): Token
    {
        return (new Parser())->parse($token);
    }

    /**
     * @return string
     * @throws ModelException
     */
    protected function getTokenIssuer(): string
    {
        $issuer = envValue('TOKEN_ISS', null);
        if (null === $issuer) {
            throw new ModelException('Undefined TOKEN_ISS env value');
        }
        return $issuer;
    }

    /**
     * Returns the default audience for the tokens
     *
     * @return string
     * @throws ModelException
     */
    protected function getTokenAudience(): string
    {
        /** @var string $audience */
        $audience = envValue('TOKEN_AUDIENCE', null);
        if (empty($audience)) {
            throw new ModelException('Empty envValue TOKEN_AUDIENCE');
        }

        return $audience;
    }

    /**
     * Returns the time the token is issued at
     *
     * In Vökuró this is calculated using the function `time()`
     * I used the session created_at
     * ¿The dates should come from db or php?
     *
     * @return int
     * @throws ModelException
     */
    protected function getTokenTimeIssuedAt(): int
    {
//        $time = 'now';
//        $time = $this->getSession()->get('created_at');
//        return (new \DateTime($time))->getTimestamp();
        return $this->getSession()->getCreatedAt()->getTimestamp();
    }

    /**
     * Returns the time drift i.e. token will be valid not before
     *
     * @return int
     * @throws ModelException
     */
    protected function getTokenTimeNotBefore(): int
    {
        return ($this->getTokenTimeIssuedAt() + envValue('TOKEN_NOT_BEFORE', 10));
    }

    /**
     * Returns the expiry time for the token
     *
     * @return int
     * @throws ModelException
     */
    protected function getTokenTimeExpiration(): int
    {
        return ($this->getTokenTimeIssuedAt() + envValue('TOKEN_EXPIRATION', 86400));
    }

    /**
     * Returns the content of the private key, defined in the config file in the path `app.privateKey`
     *
     * @return string
     * @throws InternalServerErrorException
     */
    protected function getPrivateKey() :string
    {
        if (true !== file_exists($this->config->path('app.privateKey'))) {
            throw new InternalServerErrorException('Private key file not found');
        }

        if (false === ($rtn = file_get_contents($this->config->path('app.privateKey')))) {
            throw new InternalServerErrorException('Private key file not readable');
        }

        return $rtn;
    }
}
