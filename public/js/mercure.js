const url = new URL('http://localhost:3000/hub');
url.searchParams.append('topic', 'http://127.0.0.1:8001/dons/{id}/select');
url.searchParams.append('topic', 'http://127.0.0.1:8001/dons/{id}/deselect');

const eventSource = new EventSource(url);


eventSource.onmessage = e => console.log(e);