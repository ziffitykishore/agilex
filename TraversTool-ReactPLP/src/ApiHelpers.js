import fetch from 'isomorphic-fetch';
import 'es6-promise/auto';

export async function fetchContent(url, categoryId) {
  const prefix = process.env.API_CORS_PREFIX || '';
  const content = await fetch(`${prefix}${url}/${categoryId}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'Cache-Control': 'no-cache',
    }
  }).then(parseJSON);
  try {
    return content;
  } catch (err) {
    console.log(err);
  }
}

export function parseJSON(response) {
  return response.json();
}
