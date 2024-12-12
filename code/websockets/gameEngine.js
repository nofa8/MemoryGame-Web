exports.createGameEngine = () => {
    const initGame = (gameFromDB) => {
        gameFromDB.gameStatus = 0; //
        // 0 -> game has started and running
        // 1 -> player 1 is the winner
        // 2 -> player 2 is the winner
        // 3 -> draw
        gameFromDB.currentPlayer = 1; // Player 1 starts
        gameFromDB.pairsDiscovered = { 1: 0, 2: 0 };
        gameFromDB.turns = { 1: 0, 2: 0 };
        gameFromDB.size = gameFromDB.board.cols * gameFromDB.board.rows;
        gameFromDB.board = createCardPairs(
            gameFromDB.board.cols * gameFromDB.board.rows
        );
        gameFromDB.matchedPairs = [];
        gameFromDB.flippedCards = [];
        gameFromDB.lastAction = { 1: 0, 2: 0 };
        // gameFromDB.autoStart = true;
        // gameFromDB.stopwatch = useStopwatch(gameFromDB.autoStart);
        return gameFromDB;
    };

    const createCardPairs = (boardSize) => {
        const cardTypes = ["e", "o", "p", "c"];
        const cardValues = [1, 2, 3, 4, 5, 6, 7, 11, 12, 13];

        const cardCombinations = cardTypes.flatMap((type) =>
            cardValues.map((value) => `${type}${value}`)
        );

        const selectedCombinations = cardCombinations
            .sort(() => Math.random() - 0.5)
            .slice(0, boardSize / 2);

        const shuffledCards = [...selectedCombinations, ...selectedCombinations]
            .sort(() => Math.random() - 0.5)
            .map((cardCode, index) => ({
                id: index,
                value: cardCode,
                isFlipped: false,
                isMatched: false,
            }));

        return shuffledCards;
    };

    const pairsDiscoveredFor = (game, player_id) => {
        game.pairsDiscovered[player_id] += 1;
    };
    // returns whether the game as ended or not
    const gameEnded = (game) => game.gameStatus !== 0;

    // Plays a specific piece of the game (defined by its index)
    // Returns true if the game play is valid or an object with an error code and message otherwise;
    const play = (game, index, playerSocketId, roomName, io) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 10,
                errorMessage: "You are not playing this game!",
            };
        }
        if (gameEnded(game)) {
            return {
                errorCode: 11,
                errorMessage: "Game has already ended!",
            };
        }
        if (
            (game.currentPlayer == 1 &&
                playerSocketId != game.player1SocketId) ||
            (game.currentPlayer == 2 && playerSocketId != game.player2SocketId)
        ) {
            return {
                errorCode: 12,
                errorMessage: "Invalid play: It is not your turn!",
            };
        }
        const card = game.board[index];

        if (card.isFlipped || card.isMatched) {
            return {
                errorCode: 13,
                errorMessage: "Invalid move: card already flipped or matched!",
            };
        }

        // Flip the card
        card.isFlipped = true;
        game.flippedCards.push(card);
        io.to(roomName).emit("gameChanged", game);
        // game.lastAction[game.currentPlayer] = game.stopwatch.
        if (game.flippedCards.length === 2) {
            game.turns[game.currentPlayer] += 1;

            const [firstCard, secondCard] = game.flippedCards;

            if (firstCard.value === secondCard.value) {
                firstCard.isMatched = true;
                secondCard.isMatched = true;
                game.matchedPairs.push(firstCard.value);
                game.flippedCards = [];
                pairsDiscoveredFor(game, game.currentPlayer);
                changeGameStatus(game);
                io.to(roomName).emit("gameChanged", game);
            } else {
                setTimeout(() => {
                    firstCard.isFlipped = false;
                    secondCard.isFlipped = false;

                    // Clear flipped cards and change the current player
                    game.flippedCards = [];
                    game.currentPlayer = game.currentPlayer === 1 ? 2 : 1;

                    // Emit the updated game state after flipping the cards back down
                    io.to(roomName).emit("gameChanged", game);
                }, 1000);
            }

            //   changeGameStatus(game);
        }

        return true;
    };

    const changeGameStatus = (game) => {
        if (isGameComplete(game)) {
            if (game.pairsDiscovered[1] === game.pairsDiscovered[2]) {
                game.gameStatus = 3; // Draw
            } else {
                game.gameStatus =
                    game.pairsDiscovered[1] > game.pairsDiscovered[2] ? 1 : 2; // Player 1 or 2 wins
            }
            game.status = "E";
        }
    };

    const isGameComplete = (game) => {
        return game.matchedPairs.length === game.size / 2;
    };

    // One of the players quits the game. The other one wins the game
    const quit = (game, playerSocketId) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 10,
                errorMessage: "You are not playing this game!",
            };
        }
        if (gameEnded(game)) {
            return {
                errorCode: 11,
                errorMessage: "Game has already ended!",
            };
        }
        game.gameStatus = playerSocketId == game.player1SocketId ? 2 : 1;
        game.status = "E";
        return true;
    };
    // Check if socket can close the game (game must have ended and player must belong to game)
    const close = (game, playerSocketId) => {
        if (
            playerSocketId != game.player1SocketId &&
            playerSocketId != game.player2SocketId
        ) {
            return {
                errorCode: 10,
                errorMessage: "You are not playing this game!",
            };
        }
        if (!gameEnded(game)) {
            return {
                errorCode: 14,
                errorMessage: "Cannot close a game that has not ended!",
            };
        }
        return true;
    };
    return {
        initGame,
        gameEnded,
        play,
        quit,
        close,
    };
};
