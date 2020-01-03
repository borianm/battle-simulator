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
    <h1>Game {{ $id }}</h1>
    <div style="display: flex;">
        <div id="game-details" style="width: 33%; padding: 5px;">
            <div>Status: <span id="status"></span></div>
            <div id="armies"></div>
        </div>
        <div style="width: 33%; padding: 5px;">
            <h1>Battle log</h1>
            <div id="battle-log" style="max-height: 250px; overflow: hidden; overflow-y: scroll;"></div>
        </div>
        <div id="add-army" style="width: 33%; padding: 5px;">
            <h1>Add Army</h1>
            <input id="name" placeholder="Name">
            <br>
            <input id="units" placeholder="Units (80-100)">
            <br>
            <select id="strategy">
                @foreach($strategies as $strategy)
                <option value="{{ $strategy->id }}">{{ $strategy->name }}</option>
                @endforeach
            </select>
            <br>
            <button onclick="addArmy()">Add army</button>
        </div>
    </div>
    <button onclick="run()">Start game / Run attack</button>
    <button onclick="reset()">Reset game</button>
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

        function loadGame() {
            request('{{route("games_show", ["id" => $id])}}').then((response) => {
                response.json().then((data) => {

                    if (data.data.status == 0) {
                        document.getElementById('status').innerHTML = 'Waiting';
                    } else if (data.data.status == 1) {
                        document.getElementById('status').innerHTML = 'In progress';
                    } else {
                        document.getElementById('status').innerHTML = 'Completed';
                    }
                    let armies_elem = document.getElementById('armies');
                    armies_elem.innerHTML = '';
                    for (let army of data.data.armies) {
                        let strategy = getStrategy(army.attack_strategy_id);
                        let status = getStatus(army.status);
                        let turn_made = getTurnMade(army.turn_made);
                        armies_elem.innerHTML += `
                            <p>Army name: <b>${army.name}</b> - Units: ${army.alive_units}/${army.units} - Attack Strategy: ${strategy} - Status: ${status} - Turn made: ${turn_made}</p>
                        `;
                    }
                });
            });
        }

        function loadBattleLog() {
            request('{{route("games_log", ["id" => $id])}}').then((response) => {
                response.json().then((data) => {
                    let elem = document.getElementById('battle-log');
                    elem.innerHTML = '';
                    for (let log of data.data) {
                        elem.innerHTML += `
                            <p>${log.log_text}</p>
                        `;
                    }
                });
            });
        }

        function getTurnMade(turn_made) {
            if (turn_made == 0) {
                return 'No';
            } else {
                return 'Yes';
            }
        }

        function getStatus(status) {
            if (status == 0) {
                return 'Alive';
            } else {
                return 'Dead';
            }
        }

        function getStrategy(id) {
            if (id == 1) {
                return 'Random';
            } else if (id == 2) {
                return 'Weakest';
            } else {
                return 'Strongest';
            }
        }

        function addArmy() {
            let name = document.getElementById('name').value;
            let units = document.getElementById('units').value;
            let strategy = document.getElementById('strategy').value;
            request('{{route("games_army", ["id" => $id])}}', 'POST', {
                name,
                units,
                attack_strategy_id: strategy
            }).then(response => {
                if (response.ok) {
                    loadGame();
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

        function run() {
            request('{{route("games_run", ["id" => $id])}}', 'POST').then(response => {
                if (response.ok) {
                    loadGame();
                    loadBattleLog();
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
            })
        }

        function reset() {
            request('{{route("games_reset", ["id" => $id])}}').then(response => {
                if (response.ok) {
                    loadGame();
                    loadBattleLog();
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
            })
        }

        (function() {
            loadGame();
            loadBattleLog();
        })();
    </script>
</body>

</html>