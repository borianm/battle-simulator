<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Battle Simulator</title>
</head>

<body>
    <h1 style="opacity: 0; color: red;" id="error"></h1>
    <h1 style="opacity: 0; color: green;" id="success"></h1>
    <h1>Select a game</h1>
    <select id="game-selector"></select>
    <button onclick="goToGame()">Go to game</button>
    <button onclick="createGame()">Create a game</button>
    <script>
        async function request(url, method = 'GET', body = {}) {
            let response;
            if (method === 'GET') {
                response = fetch(url);
            } else {
                response = fetch(url, {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    method: method,
                    body: JSON.stringify(body)
                });
            }
            return await response;
        }

        function loadGames() {
            request('{{route("games_get")}}').then((response) => {
                response.json().then(data => {
                    document.getElementById('game-selector').innerHTML = '';
                    for (let game of data.data) {
                        let option = document.createElement('option');
                        option.value = game.id;
                        option.innerHTML = `ID: ${game.id}`;
                        document.getElementById('game-selector').appendChild(option);
                    }
                });
            });
        }

        function goToGame() {
            let value = document.getElementById('game-selector').value;
            if (value != '') {
                location.href = `./games/${value}`;
            }
        }

        function createGame() {
            request('{{route("games_post")}}', 'POST').then((response) => {
                if (response.ok) {
                    loadGames();
                    response.json().then(data => {
                        let success = document.getElementById('success');
                        success.innerHTML = `${data.message}`;
                        success.style = 'color:green;';
                        setTimeout(() => {
                            success.style = 'opacity:0;color:green;';
                        }, 3000);
                    });
                } else {
                    response.json().then(data => {
                        let error = document.getElementById('error');
                        error.innerHTML = `${data.message}`;
                        error.style = 'color:red;';
                        setTimeout(() => {
                            error.style = 'opacity:0;color:red;';
                        }, 3000);
                    });
                }
            });
        }

        (function() {
            loadGames();
        })();
    </script>
</body>

</html>