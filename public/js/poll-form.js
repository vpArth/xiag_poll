class PollForm {
  question = '';
  answers  = [
    '',
  ];

  constructor(api) {
    this.api = api
  }

  render() {
    const table = document.createElement('table');
    table.classList.add('poll-table');
    const thead     = document.createElement('thead');
    thead.innerHTML = `<tr><th>Question:</th><th><input class="input-text" required value="${this.question}" placeholder="Enter your question here"/></th><th></th></tr>`
    thead.querySelector('input').addEventListener('input', ev => this.question = ev.target.value);

    const tbody = document.createElement('tbody');
    this.answers.forEach((answer, i) => {
      const tr     = document.createElement('tr');
      tr.innerHTML = `<th>Answer ${i + 1}</th><td><input class="input-text" required value="${answer}" placeholder="Enter the answer variant here"/></td><td><button class="btn btn--remove">x</button></td>`;
      tr.querySelector('input').addEventListener('input', ev => this.answers[i] = ev.target.value);
      tr.querySelector('.btn--remove').addEventListener('click', () => this.removeAnswer(i));
      tbody.appendChild(tr);
    });

    { // Add answer row
      const tr     = document.createElement('tr');
      tr.innerHTML = '<td class="poll-table__plus"><button class="btn btn--plus">+</button></td><td>&nbsp;</td><td>&nbsp;</td>'
      tr.querySelector('button').addEventListener('click', () => this.addAnswerSection());
      tbody.appendChild(tr);
    }

    table.appendChild(thead);
    table.appendChild(tbody);

    const old = document.querySelector('table.poll-table');
    old.parentNode.removeChild(old);

    const poll = document.querySelector('.poll');
    poll.insertBefore(table, poll.querySelector('.btn--start'));
  }
  addAnswerSection() {
    this.answers.push('');
    this.render();
  }
  removeAnswer(i) {
    this.answers.splice(i, 1);
    this.render();
  }
  submit() {
    const form = document.querySelector('form.poll');

    if (this.answers.length < 2) {
      return this._error('There must be at least two possible answers')
    }
    if (!form.checkValidity || form.checkValidity()) {
      return this.api.createPoll(this.question, this.answers)
        .then(poll => {
          location.href = `/${poll.uuid}`;
        })
        .catch(err => this._error(err));
    }
  }
  _error(err, ttl = 4000) {
    const form                              = document.querySelector('form.poll');
    form.querySelector('.errors').innerText = err.message || err;
    setTimeout(() => form.querySelector('.errors').innerText = '', ttl);
  }
}
