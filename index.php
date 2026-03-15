<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>记分系统</title>
    <style>

        .main {width: 80%; margin: 0 auto; text-align: center;}

    </style>
</head>
<body>
<DIV  CLASS="main">
    <H1>九球追分系统</H1>
    </STYLE>
    <h2>
        <!DOCTYPE html>
        <html>
        <head>
            <title>九球三人追分记分板</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 20px;
                }
                .players {
                    display: flex;
                    justify-content: space-around;
                    margin-bottom: 20px;
                }
                .player {
                    border: 2px solid #3498db;
                    border-radius: 10px;
                    padding: 15px;
                    width: 28%;
                }
                .score {
                    font-size: 48px;
                    font-weight: bold;
                    margin: 10px 0;
                }
                button {
                    padding: 8px 15px;
                    margin: 5px;
                    cursor: pointer;
                }
                .action-buttons {
                    margin-top: 20px;
                }
                .selected {
                    background-color: #f39c12;
                    color: white;
                }
                #log {
                    margin-top: 20px;
                    text-align: left;
                    border-top: 1px solid #ddd;
                    padding-top: 10px;
                    height: 100px;
                    overflow-y: auto;
                }
            </style>
        </head>
        <body>
        <h1>九球三人追分记分板</h1>

        <div class="players">
            <!-- 三位玩家 -->
            <div class="player" onclick="selectPlayer(0)">
                <h2>玩家A</h2>
                <div class="score" id="score0">100</div>
            </div>
            <div class="player" onclick="selectPlayer(1)">
                <h2>玩家B</h2>
                <div class="score" id="score1">100</div>
            </div>
            <div class="player" onclick="selectPlayer(2)">
                <h2>玩家C</h2>
                <div class="score" id="score2">100</div>
            </div>
        </div>

        <div class="action-buttons">
            <!-- 功能按钮 -->
            <button onclick="foul()">犯规（先选犯规方）</button>
            <button onclick="normalWin()">普胜 +4（先选输家）</button>
            <button onclick="golden9()">黄金9（选赢家）</button>
            <button onclick="bigWin()">大金（选赢家）</button>
            <button onclick="smallWin()">小金 +7（先选输家）</button>
            <button onclick="undo()">复位（上一步）</button>
            <button onclick="resetAll()">重置（全部100分）</button>
        </div>

        <div id="log">操作记录将会显示在这里...</div>

        <script>
            // 初始数据
            let scores = [100, 100, 100];
            let selectedPlayer = null;
            let history = [JSON.stringify(scores)]; // 历史记录用于撤销

            // 选择玩家
            function selectPlayer(playerIndex) {
                document.querySelectorAll('.player').forEach(p => p.classList.remove('selected'));
                document.querySelector(`.player:nth-child(${playerIndex+1})`).classList.add('selected');
                selectedPlayer = playerIndex;
            }

            // 更新分数显示
            function updateScores() {
                for (let i = 0; i < 3; i++) {
                    document.getElementById(`score${i}`).textContent = scores[i];
                }
            }

            // 记录操作历史
            function saveHistory() {
                history.push(JSON.stringify(scores));
                if (history.length > 50) history.shift(); // 限制历史记录数量
            }

            // 添加日志
            function addLog(message) {
                const logElement = document.getElementById('log');
                logElement.innerHTML = `${new Date().toLocaleTimeString()}: ${message}<br>` + logElement.innerHTML;
            }

            // ------ 核心功能 ------
            function foul() {
                // (1) 选择犯规玩家（loser）
                const loserLetter = prompt("请输入犯规的玩家（A/B/C）").toUpperCase();
                if (!["A", "B", "C"].includes(loserLetter)) {
                    alert("无效输入！");
                    return;
                }
                const loserIndex = loserLetter.charCodeAt(0) - 65; // A→0, B→1, C→2

                // (2) 选择赢家（winner）
                const winnerLetter = prompt("请输入赢家（A/B/C）").toUpperCase();
                if (!["A", "B", "C"].includes(winnerLetter) || winnerLetter === loserLetter) {
                    alert("无效输入或赢家不能是犯规玩家！");
                    return;
                }
                const winnerIndex = winnerLetter.charCodeAt(0) - 65;

                // (3) 执行分数变动
                saveHistory();
                scores[loserIndex] -= 1; // 犯规者扣1分
                scores[winnerIndex] += 1; // 赢家加1分
                updateScores();
                addLog(`犯规：玩家${loserLetter} -1，玩家${winnerLetter} +1`);
            }


            function normalWin() {
                if (selectedPlayer === null) {
                    alert('请选择赢家');
                    return;
                }

                const loser = prompt("请选择输家（A/B/C）").toUpperCase(); // 手动输入输家
                if (!["A", "B", "C"].includes(loser)) {
                    alert("无效选择！");
                    return;
                }

                saveHistory();
                const winner = selectedPlayer;
                const loserIndex = loser.charCodeAt(0) - 65; // A→0, B→1, C→2
                scores[winner] += 4;  // 赢家+4
                scores[loserIndex] -= 4; // 指定输家-4
                updateScores();
                addLog(`普胜：玩家${String.fromCharCode(65+winner)} +4，玩家${loser} -4`);
                selectedPlayer = null;
            }


            function golden9() {
                if (selectedPlayer === null) {
                    alert('请先选择赢家');
                    return;
                }

                saveHistory();
                const winner = selectedPlayer;
                scores[winner] += 8;  // 赢家+8分
                for (let i = 0; i < 3; i++) {
                    if (i !== winner) scores[i] -= 4; // 其他两家各扣4分
                }
                updateScores();
                addLog(`黄金9：玩家${String.fromCharCode(65+winner)} 获胜，其他玩家各扣4分`);
                selectedPlayer = null;
            }

            function bigWin() {
                if (selectedPlayer === null) {
                    alert('请先选择赢家');
                    return;
                }

                saveHistory();
                const winner = selectedPlayer;
                scores[winner] += 20;  // 赢家+20分
                for (let i = 0; i < 3; i++) {
                    if (i !== winner) scores[i] -= 10; // 其他两家各-10分
                }
                updateScores();
                addLog(`大金：玩家${String.fromCharCode(65+winner)} +20，其他玩家各-10`);
                selectedPlayer = null;
            }


            function smallWin() {
                if (selectedPlayer === null) {
                    alert('请先选择输家');
                    return;
                }
                const loser = selectedPlayer;
                selectedPlayer = null;

                setTimeout(() => {
                    if (selectedPlayer === null || selectedPlayer === loser) {
                        alert('请选择赢家（不能是输家）');
                        return;
                    }

                    saveHistory();
                    scores[loser] -= 7;   // 输家扣7分
                    scores[selectedPlayer] += 7; // 赢家加7分
                    updateScores();
                    addLog(`小金玩家${String.fromCharCode(65+selectedPlayer)} +7 (玩家${String.fromCharCode(65+loser)} -7)`);
                    selectedPlayer = null;
                }, 10);
            }

            function undo() {
                if (history.length > 1) {
                    history.pop(); // 移除当前状态
                    scores = JSON.parse(history[history.length-1]);
                    updateScores();
                    addLog("撤销到上一步");
                } else {
                    alert("无法继续撤销");
                }
            }

            function resetAll() {
                saveHistory();
                scores = [100, 100, 100];
                updateScores();
                addLog("重置所有玩家分数为100");
            }
        </script>
        </body>
        </html>

    </h2>
</DIV>
</body>
</html>