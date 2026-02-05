<?php

namespace VisitMarche\ThemeWp\Lib;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocaleHelper
{
    public static function getSelectedLanguage(): string
    {
        $current_lang = apply_filters('wpml_current_language', null);
        if (!$current_lang) {
            $current_lang = 'fr';
        }

        return $current_lang;
    }

    public static function iniTranslator(): TranslatorInterface
    {
        $yamlLoader = new YamlFileLoader();

        $translator = new Translator(self::getSelectedLanguage());
        $translator->addLoader('yaml', $yamlLoader);
        $translator->addResource('yaml', get_template_directory().'/translations/messages.fr.yaml', 'fr');
        $translator->addResource('yaml', get_template_directory().'/translations/messages.en.yaml', 'en');
        $translator->addResource('yaml', get_template_directory().'/translations/messages.nl.yaml', 'nl');

        return $translator;
    }

    public static function translate(string $text): string
    {
        $translator = self::iniTranslator();
        $language = self::getSelectedLanguage();

        return $translator->trans($text, [], null, $language);
    }
}
