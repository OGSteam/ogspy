<?php
/*
Ce script vous permet de connaître toutes les fonctions disponibles sur votre serveur
Il vous permet également d'accéder facilement à la documentation française de cette
fonction sur le site officiel de PHP
Ce Script ne fonctionne qu'avec les versions 4.0 et supérieur de PHP

Auteurs : julp et hachesse
*/
?>

<a name="top">
    <h2>Liste des modules disponibles:</h2>
    <ol start="1" type="I">
        <?php
        $listModule = get_loaded_extensions();
        foreach ($listModule as $moduleKey => $module) {
            echo "\t<li>Module : <b><a href=\"#$module\">$module</a></b><br>\n";
        }
        ?>
    </ol>
    <br/><br/>

    <h2>Detail des fonctions par module:</h2>
    <ul>
        <?php
        foreach ($listModule as $moduleKey => $module) {
            echo "\t<a name=\"$module\"><table border=\"2\" align=\"center\" width=\"90%\">\n";
            echo "\t\t<tr>\n\t\t\t<td align=\"center\">\n";
            echo "\t\t\t\t<li>Module : <b>$module</b><br>\n";
            echo "\t\t\t</td>\n\t\t</tr>\n";
            echo "\t\t<tr>\n\t\t\t<td>\n";
            $listfonctions = get_extension_funcs($module);
            sort($listfonctions);
            echo "\t\t\t\t<ol type=\"1\">\n";
            foreach ($listfonctions as $fonctionKey => $fonctions) {
                echo "\t\t\t\t\t<li>Fonction supportée : <a href=\"http://fr.php.net/$fonctions\">$fonctions</a><br>\n";
            }
            echo "\t\t\t\t</ol>\n";
            echo "\t\t\t</td>\n\t\t</tr>\n";
            echo "\t</table>\n";
            echo "<font size=\"2\"><a href=\"#top\">(revenir en haut de la page)</a></font>\n";
            echo "<br><br>\n";
        }
        ?>
    </ul>