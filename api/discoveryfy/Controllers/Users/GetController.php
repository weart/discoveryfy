<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Users;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Transformers\BaseTransformer;

/**
 * Class GetController
 */
class GetController extends BaseController
{
    /** @var string */
    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USERS;

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /** @var array<string,bool> */
    protected $sortFields  = [
        'id'            => true,
//        'status'        => true,
        'username'      => true,
//        'password'      => false,
//        'issuer'        => true,
//        'tokenPassword' => false,
//        'tokenId'       => false,
    ];

    /** @var string */
    protected $orderBy     = 'username';
}
