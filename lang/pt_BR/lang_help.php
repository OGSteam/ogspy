<?php
/**
 * Created by IntelliJ IDEA.
 * User: antho
 * Date: 05/08/2015
 * Time: 18:15
 */

$help["admin_server_status"] = "Quando o servidor esta desativado, somente os membros com o status de administrador acessarão as funcionalidades do servidor";
$help["admin_server_status_message"] = "A mensagem será visualizada aos membros \"de base\" quando o servidor for desativado";
$help["admin_save_transaction"] = "Les transactions correspondent aux :<br>- Systèmes solaires<br>- Rapports d'espionnage<br>- Classements joueurs et alliances";
$help["admin_member_manager"] = "Autoriza a criação, a atualização e a remoção dos usuários";
$help["admin_ranking_manager"] = "Autoriza a remoção das classificações dos jogadores e alianças";
$help["admin_check_ip"] = "Certains utilisateurs subissent des déconnexions intempestives (AOL, Proxy, etc).<br>Activez cette option afin qu'ils puissent désactiver la vérification dans leur profil";
$help["admin_session_infini"] = "Se você escolher as sessões indefinidas no tempo, vários indivíduos não poderão mais utilizar a mesma conta no mesmo tempo";
$help["drop_sessions"] = "Esvazie a tabela das sessões, isto alivia a administração mas obriga todos os usuários a se reconectar.";

$help["search_strict"] = "<font color=orange>Joueur recherché :</font><br><i>Liquid snake</i><br><font color=orange>Critère de recherche :</font><br><i>snake</i><br><br>=> <font color=lime>Résultat positif</font> si l'option \"strict\" est désactivée<br>=> <font color=red>Résultat négatif</font> si l'option \"strict\" est activée";

$help["home_commandant"] = "Página império da conta comandante";

$help["profile_login"] = "Deve conter entre 3 e 15 caracteres (os caracteres especiais não são aceitos)";
$help["profile_pseudo_email"] = "Se estiver cheio, você receberá os mails da parte de alguns mods";
$help["profile_main_planet"] = "A vista da Galáxia será aberta diretamente no seu sistema solar";
$help["profile_password"] = "Deve conter entre 6 e 15 caracteres (os caracteres especiais não são aceitos)";
$help["profile_galaxy"] = "Doit contenir un nombre<br> de 1 à 999";
$help["profile_disable_ip_check"] = "La vérification de l'adresse IP permet de vous protéger contre le vol de session.<br><br>";
$help["profile_disable_ip_check"] .= "Si vous êtes déconnecté régulièrement (AOL, Proxy, etc), désactivez la vérification.<br><br>";
$help["profile_disable_ip_check"] .= "<i>L'option est disponible uniquement si l'administrateur l'a activée</i>";

$help["galaxy_phalanx"] = "Carregue os relatórios de espionagem para visualizar os Phalanx hostis";

$help["ratio_block"] = "Você possui uma proporção inferior ao limite mínimo, você não pode acessar os mods";
$help["profile_speed_uni"] = "Indique o multiplicador de velocidade do seu universo (1 por padrão)";
$help["profile_ddr"] = "Assinale se o depósito da aliança está presente no seu universo";
$help["astro_strict"] = "Assinale se o universo é antigo. Isto permite possuir 9 planetas sem precisar da tecnologia astrofísica correspondente.";
$help["uni_arrondi"] = "Para versão Ogame >5.8.5 Em curso de projeto na Gameforge.<br> Exemplo: Distância da galáxia entre 1 e 9 = 1G (se arredondado); Distância do sistema entre 1 e 499 = 1S (se arredondado).<br> Fórmula : dist(a,b)=||a-b|-unitMax| (ou unitMax=499(system), unitMax=9(galaxy).";
$help["config_cache"] = "Duração da vida do cache configurado em segundos.";
$help["mod_cache"] = "Duração da vida do cache mod em segundos.";

/* admin page */

$help['display_mips'] = "Exiba ou oculte os MIPs dos usuários de OGSpy, mas só serão exibidas aqueles que podem ver as alianças protegidas.";
$help['member_stats'] = "Exiba ou oculte a tabela de estatística dos membros em baixo da página estatísticas";
$help['member_connected'] = "Affiche les (*) qui permettent de savoir qui est connecté<br>Désactivé si l'affichage des membres n'est pas activé";
$help['member_registration'] = "Exiba ou oculte a tabela contendo o link do fórum deste OGSpy";
$help['ally_name'] = "Nome da aliança deste OGSpy";
$help['forum_link'] = "Link de uma sessão do fórum, ver o MP do administrado OGSpy";
$help['first_displayed_module'] = "Módulo exibido durante a conexão dos usuários deste OGSpy";
$help['first_displayed_module_admin'] = "Módulo exibido durante a conexão dos administradores deste OGSpy";
