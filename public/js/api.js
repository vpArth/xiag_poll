class Api {
  #base;
  constructor(base = '/api/') {
    this.#base = base;
  }
  async _get(path) {
    const res = await fetch(`${this.#base}${path}`);
    return res.json();
  }
  async _post(path, data) {
    const res = await fetch(`${this.#base}${path}`, {
      method:  'post',
      headers: {
        'Accept':       'application/json, text/plain, */*',
        'Content-Type': 'application/json'
      },
      body:    JSON.stringify(data)
    });

    return res.json();
  }
}

class PollApi extends Api {
  createPoll(question, answers) {
    return this._post('poll', {question, answers});
  }

  submitVote(id_answer, username) {
    return this._post('vote', {id_answer, username});
  }

  results(uuid) {
    return this._get(`poll/${uuid}/results`);
  }
}
