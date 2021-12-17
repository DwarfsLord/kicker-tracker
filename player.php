<html>
<head>
    <title>Bode Kicker Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id='root'>
        <div class='box header'>
            <div class='title'>
                Kicker Elo
            </div>
            <div class='config'>
                <div class='new_game'>
                    Adding player...
                </div>
            </div>
        </div>
        <form action="/index.php" class='box add_player'>
            <div class='add_player_label'>
                <label for="field_name">Name:</label>
            </div>
            <div class='add_player_field'>
                <input type="text" id="field_name" name="name" class="field">
            </div>
            <div class='add_player_ack'>
                <input type="submit" value="Go!" class="submit">
            </div>
        </form>
    </div>
</body>
</html>