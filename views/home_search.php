
<style>
.og-player-selector {
    margin-bottom: 20px;
    padding: 15px;
    background-color: var(--color-table-background, #272727);
    border-radius: 8px;
    border: 1px solid var(--color-table-border, rgba(255, 255, 255, 0.1));
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
.og-player-selector .search-container {
    position: relative;
    flex: 1;
    min-width: 300px;
}
.og-player-selector input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    background-color: var(--color-button-background, #000000);
    color: var(--color-text, white);
    border: 1px solid var(--color-button-border, grey);
    border-radius: 4px;
    box-sizing: border-box;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}
.og-player-selector input[type="text"]:focus {
    outline: none;
    border-color: var(--color-highlight, #4682B4);
    box-shadow: 0 0 0 2px rgba(70, 130, 180, 0.3);
}
.og-player-selector input[type="text"]::placeholder {
    color: var(--color-table-color-tdstat, #3EBAFD);
    opacity: 0.7;
}
.og-player-selector .dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: var(--color-table-background, #272727);
    border: 1px solid var(--color-highlight, #4682B4);
    border-top: none;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    border-radius: 0 0 4px 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.5);
}
.og-player-selector .dropdown-item {
    padding: 10px 12px;
    cursor: pointer;
    color: var(--color-text, white);
    border-bottom: 1px solid var(--color-table-border, rgba(255, 255, 255, 0.1));
    transition: background-color 0.2s ease;
}
.og-player-selector .dropdown-item:hover {
    background-color: var(--color-table-background-hover, #191919);
}
.og-player-selector .dropdown-item:last-child {
    border-bottom: none;
}
.og-player-selector .btn {
    padding: 10px 16px;
    border: 1px solid var(--color-button-border, grey);
    border-radius: 4px;
    cursor: pointer;
    white-space: nowrap;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.1s ease;
    text-decoration: none;
    display: inline-block;
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    font-size: 11px;
}
.og-player-selector .btn:hover {
    transform: translateY(-1px);
    background-color: var(--color-button-hover, rgb(40, 40, 40));
}
.og-player-selector .btn-primary {
    background-color: var(--color-highlight, #4682B4);
    color: var(--color-text, white);
}
.og-player-selector .btn-primary:hover {
    background-color: var(--color-navbarmenu-link, #26f33d);
}
.og-player-selector .btn-secondary {
    background-color: var(--color-button-background, #000000);
    color: var(--color-button-color, #ffffff);
}
.og-player-selector .btn-secondary:hover {
    background-color: var(--color-button-hover, rgb(40, 40, 40));
}
.og-player-selector .current-selection {
    margin-top: 12px;
    padding: 8px 12px;
    background-color: var(--color-table-background-hover, #191919);
    border-radius: 4px;
    border-left: 4px solid var(--color-table-color-tdstat, #3EBAFD);
    font-size: 12px;
    color: var(--color-text, white);
}
.og-player-label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: var(--color-highlight, #3EBAFD);
    font-size: 15px;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}
.og-myplayer-btn {
    background: linear-gradient(90deg, var(--color-highlight, #3EBAFD) 60%, var(--color-success, #436443) 100%);
    color: #fff !important;
    font-weight: bold;
    border: 2px solid var(--color-highlight, #3EBAFD);
    box-shadow: 0 2px 8px rgba(62,186,253,0.15);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 13px;
    transition: background 0.2s, border 0.2s, color 0.2s;
}
.og-myplayer-btn:hover {
    background: linear-gradient(90deg, var(--color-success, #436443) 60%, var(--color-highlight, #3EBAFD) 100%);
    color: #fff !important;
    border: 2px solid var(--color-success, #436443);
}
@media (max-width: 768px) {
    .og-player-selector .controls {
        flex-direction: column;
        gap: 10px;
    }
    .og-player-selector .search-container {
        min-width: 100%;
    }
}
</style>

<div class="og-player-selector">
    <div class="og-form-group">
        <label for="player-search" class="og-player-label">
            <?php echo isset($lang['HOME_EMPIRE_SELECT_PLAYER']) ? $lang['HOME_EMPIRE_SELECT_PLAYER'] : 'Sélectionner un joueur :'; ?>
        </label>
        <div class="controls" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <div class="search-container">
                <input type="text" 
                       id="player-search" 
                       placeholder="<?php echo isset($lang['HOME_EMPIRE_SEARCH_PLAYER']) ? $lang['HOME_EMPIRE_SEARCH_PLAYER'] : 'Rechercher un joueur...'; ?>"
                       autocomplete="off">
                <div id="player-dropdown" class="dropdown"></div>
            </div>
            <button type="button" id="reset-to-my-player" class="btn btn-primary og-myplayer-btn">
                <?php echo isset($lang['HOME_EMPIRE_MY_PLAYER']) ? $lang['HOME_EMPIRE_MY_PLAYER'] : 'Mon joueur'; ?>
            </button>
        </div>
        <input type="hidden" id="selected-player-id" value="<?php echo $selected_player_id; ?>">
        <div id="current-selection" class="current-selection">
            <strong><?php echo isset($lang['HOME_EMPIRE_CURRENT_PLAYER']) ? $lang['HOME_EMPIRE_CURRENT_PLAYER'] : 'Joueur actuel :'; ?></strong> 
            <span id="current-player-name"><?php echo htmlspecialchars($player_data['name']); ?></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const playerSearch = document.getElementById('player-search');
    const playerDropdown = document.getElementById('player-dropdown');
    const selectedPlayerIdInput = document.getElementById('selected-player-id');
    const currentPlayerName = document.getElementById('current-player-name');
    const resetToMyPlayerBtn = document.getElementById('reset-to-my-player');
    
    let allPlayers = <?php echo json_encode($all_players); ?>;
    let currentUserId = <?php echo $user_data['player_id']; ?>;
    let currentUserName = <?php echo json_encode($user_player_name); ?>;
    
    // Ajouter le joueur actuel à la liste s'il n'y est pas
    let userPlayerExists = allPlayers.some(player => player.id == currentUserId);
    if (!userPlayerExists && currentUserId && currentUserName) {
        allPlayers.unshift({id: currentUserId.toString(), name: currentUserName});
    }
    
    function filterPlayers(searchTerm) {
        return allPlayers.filter(player => 
            player.name.toLowerCase().includes(searchTerm.toLowerCase())
        ).slice(0, 10); // Limiter à 10 résultats
    }
    
    function showDropdown(players) {
        if (players.length === 0) {
            playerDropdown.style.display = 'none';
            return;
        }
        
        playerDropdown.innerHTML = '';
        players.forEach(player => {
            const item = document.createElement('div');
            item.className = 'dropdown-item';
            item.textContent = player.name;
            item.dataset.playerId = player.id;
            
            if (player.id == currentUserId) {
                item.innerHTML += ' <span style="color: #28a745; font-weight: bold;">(Mon joueur)</span>';
            }
            
            item.addEventListener('click', function() {
                selectPlayer(player.id, player.name);
            });
            
            playerDropdown.appendChild(item);
        });
        
        playerDropdown.style.display = 'block';
    }
    
    function hideDropdown() {
        setTimeout(() => {
            playerDropdown.style.display = 'none';
        }, 200);
    }
    
    function selectPlayer(playerId, playerName) {
        selectedPlayerIdInput.value = playerId;
        currentPlayerName.textContent = playerName;
        playerSearch.value = playerName;
        hideDropdown();
        
        // Rediriger vers la nouvelle sélection
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('selected_player_id', playerId);
        window.location.href = currentUrl.toString();
    }
    
    // Event listeners
    playerSearch.addEventListener('input', function() {
        const searchTerm = this.value;
        if (searchTerm.length >= 1) {
            const filteredPlayers = filterPlayers(searchTerm);
            showDropdown(filteredPlayers);
        } else {
            hideDropdown();
        }
    });
    
    playerSearch.addEventListener('focus', function() {
        if (this.value.length >= 1) {
            const filteredPlayers = filterPlayers(this.value);
            showDropdown(filteredPlayers);
        }
    });
    
    playerSearch.addEventListener('blur', hideDropdown);
    
    resetToMyPlayerBtn.addEventListener('click', function() {
        if (currentUserId && currentUserName) {
            selectPlayer(currentUserId, currentUserName);
        }
    });
    
    // Définir la valeur initiale du champ de recherche
    playerSearch.value = <?php echo json_encode($player_data['name']); ?>;
});
</script>
