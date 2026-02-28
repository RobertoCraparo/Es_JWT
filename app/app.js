const API_BASE = 'http://localhost/Es_JWT'; // es: http://localhost/api

const loginForm = document.getElementById('loginForm');
const regForm = document.getElementById('regForm');
const output = document.getElementById('output');
const newTaskForm = document.getElementById('newTaskForm');

if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

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
}


if (regForm) {
    regForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        console.log(username, password);
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

            output.textContent = 'Registrazione effettuata con successo';
        } catch (err) {
            output.textContent = err.message;
        }
    })
}

if (newTaskForm) {
    newTaskForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const task = document.getElementById('task').value.trim();

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
}


async function getUsers() {
    const token = localStorage.getItem('token');

    if (!token) {
        output.textContent = 'Token non trovato. Effettua il login.';
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/users`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.error || 'Non autorizzato');
        }

        const data = await response.json();
        let s = "Utenti:\n";
        data.forEach((user) => {
            s += `${user.id} ${user.username}\n`;
        });
        output.textContent = s;

    } catch (err) {
        output.textContent = err.message;
    }
}

const btnUsers = document.getElementById('btn-users');
if (btnUsers) {
    btnUsers.addEventListener('click', getUsers);
}
