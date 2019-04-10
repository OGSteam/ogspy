<?php
/**
 * OGSpy Admin functions
 * PHP Version 7.3
 * 
 * @category   Gametool
 * @package    OGSpy
 * @subpackage Common
 * @author     DarkNoon29 <darknoon@darkcity.fr>
 * @copyright  2019 OGSteam.fr
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT:<GIT_ID>
 * @link       https://ogsteam.fr  
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}


/**
 * Formatting the query for the Github API
 * 
 * @param string $request Content
 * 
 * @return string $data Github Data
 */
function github_api_Request($request) 
{

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: OGSpy',
                'Authorization: token d08499607a0f2469405465cf29e3aeb9d4b1265f'
            ]
        ]
    ];

    $context = stream_context_create($opts);

    try {
        $data = file_get_contents($request, false, $context);

        if ($data === false) {
            log_('mod', "[ERROR_github_Request] Unable to get: " . $request);
        }
    } catch (Exception $e) {
        log_('mod', "[ERROR_github_Request] Exception: " . $e->getMessage());
    }

    return $data;
}

/**
 * Formatting the query for the Github API
 * 
 * @param string $repository Git Repository
 * 
 * @return array $release
 */
function github_get_latest_release($repository)
{
    $release = 'no_release_available';
    $description = 'NA';
    $url = "https://api.github.com/repos/ogsteam/".$repository."/releases/latest";
    $data = github_api_Request(url);

    $mod_data = json_decode($data, true);

    if (isset($mod_data) ) {

        $release = $mod_data['tag_name'];
        $description = $mod_data['body'];
        $description = str_replace("\n", "<br>", $description);
    }

    return array('release' => $release, 'description' => $description);

}