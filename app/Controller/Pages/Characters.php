<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Controller\Pages;

use App\Model\Entity\Account;
use \App\Utils\View;
use App\Model\Entity\Characters as Character;
use App\Model\Entity\Player as EntityPlayer;
use App\Model\Entity\ServerConfig as EntityServerConfig;
use App\Model\Functions\Player;
use App\Model\Functions\Server;

class Characters extends Base{
    public static function getPlayers($request, $name)
    {
        $websiteInfo = EntityServerConfig::getInfoWebsite()->fetchObject();
        date_default_timezone_set($websiteInfo->timezone);

        $postVars = $request->getPostVars();
        if (!empty($postVars['name'])) {
            $filterpost_name = filter_var($postVars['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $decodepost_name = urldecode($filterpost_name);
            if (!empty($decodepost_name)) {
                $request->getRouter()->redirect('/community/characters/'.$decodepost_name);
            }
        }
        $filter_name = filter_var($name, FILTER_SANITIZE_URL);
        $decode_name = urldecode($filter_name);
        if (!empty($decode_name)) {
            $obPlayer = EntityPlayer::getPlayer('name = "'.$decode_name.'"')->fetchObject();
            $dbAccount = Account::getAccount('id = "'.$obPlayer->account_id.'"')->fetchObject();
            if ($obPlayer == false) {
                $request->getRouter()->redirect('/community/characters');
            }
        }
        if(!empty($dbAccount)){
        $player['info'] = [
            'name' => $obPlayer->name,
            'group_id' => Player::convertGroup($obPlayer->group_id),
            'main' => $obPlayer->main,
            'comment' => $obPlayer->comment,
            'online' => Player::isOnline($obPlayer->id),
            'creation' => date('M d Y, H:i:s', strtotime($dbAccount->creation)),
            'experience' => $obPlayer->experience,
            'level' => $obPlayer->level,
            'vocation' => Player::convertVocation($obPlayer->vocation),
            'town_id' => Player::convertTown($obPlayer->town_id),
            'world' => Server::getWorldById($obPlayer->world),
            'sex' => Player::convertSex($obPlayer->sex),
            'marriage_status' => $obPlayer->marriage_status,
            'marriage_spouse' => $obPlayer->marriage_spouse,
            'bonus_rerolls' => $obPlayer->bonus_rerolls,
            'prey_wildcard' => $obPlayer->prey_wildcard,
            'task_points' => $obPlayer->task_points,
            'lookfamiliarstype' => $obPlayer->lookfamiliarstype,
            'premdays' => Player::convertPremy($obPlayer->account_id),
            'deletion' => $obPlayer->deletion,
            'hidden' => $obPlayer->hidden,
        ];
        $player['stats'] = [
            'balance' => $obPlayer->balance,
            'health' => $obPlayer->health,
            'healthmax' => $obPlayer->healthmax,
            'mana' => $obPlayer->mana,
            'manamax' => $obPlayer->manamax,
            'manashield' => $obPlayer->manashield,
            'max_manashield' => $obPlayer->max_manashield,
            'soul' => $obPlayer->soul,
            'cap' => $obPlayer->cap,
            'skull' => $obPlayer->skull,
            'skulltime' => $obPlayer->skulltime,
            'lastlogout' => Player::convertLastLogin($obPlayer->lastlogin),
            'deletion' => $obPlayer->deletion,
            'achievements_points' => Player::getAchievementPoints($obPlayer->id)
        ];
        $player['outfit'] = [
            'image_url' => Player::getOutfitImage($obPlayer->looktype, $obPlayer->lookaddons, $obPlayer->lookbody, $obPlayer->lookfeet, $obPlayer->lookhead, $obPlayer->looklegs, $obPlayer->lookmountbody),
            'lookbody' => $obPlayer->lookbody,
            'lookfeet' => $obPlayer->lookfeet,
            'lookhead' => $obPlayer->lookhead,
            'looklegs' => $obPlayer->looklegs,
            'looktype' => $obPlayer->looktype,
            'lookaddons' => $obPlayer->lookaddons,
        ];
        $player['mount'] = [
            'lookmountbody' => $obPlayer->lookmountbody,
            'lookmountfeet' => $obPlayer->lookmountfeet,
            'lookmounthead' => $obPlayer->lookmounthead,
            'lookmountlegs' => $obPlayer->lookmountlegs,
        ];
        $player['blessings'] = [
            'blessings' => $obPlayer->blessings,
            'blessings1' => $obPlayer->blessings1,
            'blessings2' => $obPlayer->blessings2,
            'blessings3' => $obPlayer->blessings3,
            'blessings4' => $obPlayer->blessings4,
            'blessings5' => $obPlayer->blessings5,
            'blessings6' => $obPlayer->blessings6,
            'blessings7' => $obPlayer->blessings7,
            'blessings8' => $obPlayer->blessings8,
        ];
        $player['skills'] = [
            'onlinetime' => $obPlayer->onlinetime,
            'stamina' => $obPlayer->stamina,
            'xpboost_stamina' => $obPlayer->deletion,
            'xpboost_value' => $obPlayer->deletion,
            'maglevel' => $obPlayer->maglevel,
            'manaspent' => $obPlayer->manaspent,
            'skill_fist' => $obPlayer->skill_fist,
            'skill_fist_tries' => $obPlayer->skill_fist_tries,
            'skill_club' => $obPlayer->skill_club,
            'skill_club_tries' => $obPlayer->skill_club_tries,
            'skill_sword' => $obPlayer->skill_sword,
            'skill_sword_tries' => $obPlayer->skill_sword_tries,
            'skill_axe' => $obPlayer->skill_axe,
            'skill_axe_tries' => $obPlayer->skill_axe_tries,
            'skill_dist' => $obPlayer->skill_dist,
            'skill_dist_tries' => $obPlayer->skill_dist_tries,
            'skill_shielding' => $obPlayer->skill_shielding,
            'skill_shielding_tries' => $obPlayer->skill_shielding_tries,
            'skill_fishing' => $obPlayer->skill_fishing,
            'skill_fishing_tries' => $obPlayer->skill_fishing_tries,
        ];
        $player['allplayers'] = Player::getAllCharacters($obPlayer->account_id);
        $player['houses'] = Player::getHouse($obPlayer->id);
        $player['achievements'] = Player::getAchievements($obPlayer->id, 30000);
        $player['guild'] = Player::getGuildMember($obPlayer->id);
        $player['deaths'] = Player::getDeaths($obPlayer->id);
        $player['frags'] = Player::getFrags($obPlayer->id);
        $player['equipaments'] = Player::getEquipaments($obPlayer->id);

        }else{
            $player = false;
        }
        return $player;
    }

    public static function getCharacters($request, $name = null, $errorMessage = null)
    {
        $content = View::render('pages/community/characters', [
            'player' => self::getPlayers($request, $name),
            'boostedcreature' => Server::getBoostedCreature(),
            'status' => $errorMessage,
        ]);
        return parent::getBase('Characters', $content, 'characters');
    }
    
}