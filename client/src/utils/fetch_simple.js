import { ENTRYPOINT } from 'src/config/entrypoint';

const MIME_TYPE = 'application/ld+json';

export default function (endpoint, options = {}) {
  console.log('fetching');
  if (typeof options.headers === 'undefined') Object.assign(options, { headers: new Headers() });

  if (options.headers.get('Accept') === null) options.headers.set('Accept', MIME_TYPE);

  if (
    options.body !== undefined
    && !(options.body instanceof FormData)
    && options.headers.get('Content-Type') === null
  ) {
    options.headers.set('Content-Type', MIME_TYPE);
  }

  if (options.params) {
    const queryString = Object.keys(options.params)
      .map(key => `${key}=${options.params[key]}`)
      .join('&');
    endpoint = `${endpoint}?${queryString}`;
  }

  const entryPoint = ENTRYPOINT + (ENTRYPOINT.endsWith('/') ? '' : '/');

  return fetch(new URL(endpoint, entryPoint), options).then((response) => {
    console.log('fetched');
    console.log(response);
    if (response.ok) return response;

    return response.json().then(json => (json['hydra:description']
      ? json['hydra:description']
      : response.statusText));
  })
    .then(response => response.json())
    .then((data) => {
      console.log(data['hydra:member']);
      return data['hydra:member'];
      // commit(types.TOGGLE_LOADING);
      // commit(types.SET_ITEMS, data['hydra:member']);
      // commit(types.SET_VIEW, data['hydra:view']);
      // commit(types.SET_TOTALITEMS, data['hydra:totalItems']);
    })
    .catch((e) => {
      console.error(e);
      // commit(types.TOGGLE_LOADING);
      // commit(types.SET_ERROR, e.message);
    });
}
