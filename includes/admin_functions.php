<?php

/**
 * OGSpy Admin functions
 * PHP Version 7.3
 *
 * @category   Gametool
 * @package    OGSpy
 * @subpackage Common
 * @author     DarkNoon29 <darknoon@darkcity.fr>
 * @copyright  2019 ogsteam.eu
 * @license    https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    GIT:<GIT_ID>
 * @link       https://ogsteam.eu
 */

use Ogsteam\Ogspy\Model\Config_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

/**
 * Formatting the query for the GitHub API
 * @param string $request Content
 * @return string|null $data Github Data
 */
function githubApiRequest($request)
{
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: OGSpy',
            ]
        ]
    ];

    $context = stream_context_create($opts);

    try {
        $headers = get_headers($request, true, $context);
        $data = file_get_contents($request, false, $context);


        if ($data === false || $headers['X-RateLimit-Remaining'] === 0) {
            log_('mod', "[ERROR_Github_API] Unable to get: " . $request);
            log_('mod', "[ERROR_Github_API] GitHub Requests Remaining : " . $headers['X-RateLimit-Remaining']);
        return null;

        }
    } catch (Exception $e) {
        log_('mod', "[ERROR_github_Request] API Response Code: " . http_response_code());
        log_('mod', "[ERROR_github_Request] Exception: " . $e->getMessage());
    }

    return $data;
}

/**
 * Formatting the query for the GitHub API
 * @param string $repository Git Repository
 * @return array $release
 */
function githubGetLatestRelease($repository)
{
    $release = 'no_release_available';
    $description = 'NA';
    $url = "https://api.github.com/repos/ogsteam/" . $repository . "/releases/latest";

    $param = ['OGSpylastRelease'];

    $currentDateminus1day= strtotime('-1 day');

    $config = new Config_Model();

    if ( empty($config->get($param) ) || ( $config->get($param)['config_value'] < $currentDateminus1day ) ) {

        $data = githubApiRequest($url);
        $config->update_one( time(), 'OGSpylastRelease');
        file_put_contents('./cache/repo_info.json', $data);
    }

    $repoData = file_get_contents('./cache/repo_info.json');


    $mod_data = json_decode($repoData, true);

    if (isset($mod_data)) {

        $release = $mod_data['tag_name'];
        $description = $mod_data['body'];
        $description = str_replace("\n", "<br>", $description);
    }

    return array('release' => $release, 'description' => $description);
}
