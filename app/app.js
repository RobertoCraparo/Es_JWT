const API_BASE = 'http://localhost/api-jwt'; // es: http://localhost/api

const form = document.getElementById('loginForm');
const regForm = document.getElementById('regForm');
const output = document.getElementById('output');
const newTaskForm = document.getElementById('newTaskForm');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({username, password})
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Errore di login');
        }

        // salva JWT
        localStorage.setItem('token', data.token);

        output.textContent = 'Login effettuato con successo';
    } catch (err) {
        output.textContent = err.message;
    }
});

regForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch(`${API_BASE}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({username, password})
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Errore di registrazione');
        }

        // salva JWT
        localStorage.setItem('token', data.token);

        output.textContent = 'Registrazione effettuata con successo';
    } catch (err) {
        output.textContent = err.message;
    }
})

newTaskForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const task = document.getElementById('task').value;

    try {
        const response = await fetch(`${API_BASE}/task`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({task})
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Errore di registrazione');
        }

        // salva JWT
        localStorage.setItem('token', data.token);

        output.textContent = 'Task creata con successo';
    } catch (err) {
        output.textContent = err.message;
    }
})

async function getUsers() {
    const token = localStorage.getItem('token');

    const response = await fetch(`${API_BASE}/users`, {
        headers: {
            'Authorization': `Bearer ${token}`
        }
    });

    if (!response.ok) {
        throw new Error('Non autorizzato');
    }

    response.json()
        .then((data) => {

            let s = "";

            data.forEach((user) => {
                s += `${user.id} ${user.name} (${user.email})\n`;
            })

            alert(s);
        });
}

document.getElementById('btn-users').addEventListener('click', getUsers);