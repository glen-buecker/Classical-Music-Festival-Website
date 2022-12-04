<?php
class Armadillo_Language
{
    public static function installLanguages()
    {
        $coreFolder = dirname(dirname(__FILE__));
        if ( file_exists($coreFolder . '/lang.zip') and !file_exists($coreFolder . '/lang') ) {
            if ( class_exists('ZipArchive') ) {
                $zip = new ZipArchive();
                $localizations = $zip->open($coreFolder . '/lang.zip');
                if ($localizations === true) {
                    $zip->extractTo($coreFolder . '/lang/');
                    $zip->close();
                } else {  }
            } else {
                require_once(dirname(__FILE__) . '/pclzip.lib.php');
                $contentEditor = new PclZip($coreFolder . '/lang.zip');
                $contentEditor->extract(PCLZIP_OPT_PATH, $coreFolder . '/lang/');
            }
        }
    }

    public static function listInstalledLanguages()
    {
        $langFolder = dirname(dirname(__FILE__)) . '/lang';
        if ( file_exists($langFolder) ) {
            $listOfLangs = array_diff(scandir($langFolder, 1), array('..', '.'));
            $localizations = array();
            if (is_array($listOfLangs) and !empty($listOfLangs)) {
                sort($listOfLangs);
                foreach ($listOfLangs as $lang) {
                    $lang = basename($lang, ".php");
                    if ($lang == 'en') { $localizations[] = array('abbr' => 'en', 'name' => 'English'); } 
                    elseif ($lang == 'bg') { $localizations[] = array('abbr' => 'bg', 'name' => 'Български'); } 
                    elseif ($lang == 'de') { $localizations[] = array('abbr' => 'de', 'name' => 'Deutsch'); } 
                    elseif ($lang == 'fr') { $localizations[] = array('abbr' => 'fr', 'name' => 'Français'); } 
                    elseif ($lang == 'it') { $localizations[] = array('abbr' => 'it', 'name' => 'Italiano'); } 
                    elseif ($lang == 'ja') { $localizations[] = array('abbr' => 'ja', 'name' => '日本語'); } 
                    elseif ($lang == 'ar') { $localizations[] = array('abbr' => 'ar', 'name' => 'العربية'); } 
                    elseif ($lang == 'cs') { $localizations[] = array('abbr' => 'cs', 'name' => 'Čeština'); } 
                    elseif ($lang == 'da') { $localizations[] = array('abbr' => 'da', 'name' => 'Dansk'); } 
                    elseif ($lang == 'es') { $localizations[] = array('abbr' => 'es', 'name' => 'Español'); } 
                    elseif ($lang == 'fi') { $localizations[] = array('abbr' => 'fi', 'name' => 'Suomi'); } 
                    elseif ($lang == 'hi') { $localizations[] = array('abbr' => 'hi', 'name' => 'हिंदी'); } 
                    elseif ($lang == 'ko') { $localizations[] = array('abbr' => 'ko', 'name' => '한국의'); } 
                    elseif ($lang == 'nb') { $localizations[] = array('abbr' => 'nb', 'name' => 'Norsk Bokmål'); } 
                    elseif ($lang == 'nl') { $localizations[] = array('abbr' => 'nl', 'name' => 'Nederlands'); } 
                    elseif ($lang == 'pl') { $localizations[] = array('abbr' => 'pl', 'name' => 'Polski'); } 
                    elseif ($lang == 'pt') { $localizations[] = array('abbr' => 'pt', 'name' => 'Português'); } 
                    elseif ($lang == 'ro') { $localizations[] = array('abbr' => 'ro', 'name' => 'Românâ'); } 
                    elseif ($lang == 'ru') { $localizations[] = array('abbr' => 'ru', 'name' => 'русский'); } 
                    elseif ($lang == 'sv') { $localizations[] = array('abbr' => 'sv', 'name' => 'Svenska'); } 
                    elseif ($lang == 'th') { $localizations[] = array('abbr' => 'th', 'name' => 'ภาษาไทย'); } 
                    elseif ($lang == 'zh') { $localizations[] = array('abbr' => 'zh', 'name' => '筒体中文'); }
                }

                return $localizations;
            }
        } else { Armadillo_Language::installLanguages(); }
    }

    // Convert the two letter language abbreviation to the full English word
    public static function langToLanguage($lang)
    {
        $lang = strtolower($lang);
        switch ($lang) {
            case 'bg':
                return 'Bulgarian';
                break;

            case 'cs':
                return 'Czech';
                break;

            case 'de':
                return 'German';
                break;

            case 'en':
                return 'English';
                break;

            case 'es':
                return 'Spanish';
                break;

            case 'fi':
                return 'Finnish';
                break;

            case 'fr':
                return 'French';
                break;

            case 'it':
                return 'Italian';
                break;

            case 'ja':
                return 'Japanese';
                break;

            case 'nl':
                return 'Dutch';
                break;

            case 'pl':
                return 'Polish';
                break;

            case 'pt':
                return 'Portuguese';
                break;

            case 'sv':
                return 'Swedish';
                break;
            
            default:
                return 'No matching language';
                break;
        }
    }

    //Display localized versions of the various messages shown to users
    public static function msg($s)
    {
        $preferredLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'en';
        $lang = isset($_SESSION['language']) ? $_SESSION['language'] : $preferredLang;
        $langFolder = dirname(dirname(__FILE__)) . "/lang/";
        if ( file_exists($langFolder) ) {
            if ( file_exists($langFolder.$lang.".php") ) { include $langFolder.$lang.".php"; } else { include $langFolder."en.php"; }

            if (isset($messages[$s])) {
                return $messages[$s];
            } else {
                // Log the error
                error_log("l10n error:LANG:" .
                    "$lang,message:'$s'");
                // Display English version as fallback
                include $langFolder."en.php";
                return $messages[$s];
            }
        } else { Armadillo_Language::installLanguages(); }
    }

    public static function public_msg($armadilloOptions,$s)
    {
        $preferredLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'en';
        $lang = isset($armadilloOptions['site_language']) ? $armadilloOptions['site_language'] : $preferredLang;
        $langFolder = dirname(dirname(__FILE__)) . "/lang/";
        if ( file_exists($langFolder) ) {
            if ( file_exists($langFolder.$lang.".php") ) { include $langFolder.$lang.".php"; } else { include $langFolder."en.php"; }

            if (isset($messages[$s])) {
                return $messages[$s];
            } else {
                error_log("l10n error:LANG:" .
                    "$lang,message:'$s'");
                // Display English version as fallback
                include $langFolder."en.php";
                return $messages[$s];
            }
        } else { Armadillo_Language::installLanguages(); }
    }
}
