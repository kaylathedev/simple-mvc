<?php

class Language
{
    private static $uncountables = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');
    private static $irregulars = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'move' => 'moves'
    );

    /**
     * Pluralizes English nouns.
     *
     * @param  string $word An English noun to pluralize.
     * @return string A plural noun.
     */
    public static function pluralize($word)
    {
        //TODO: Work on pluralizing and singularizing.
        $plurals = array(
            '#(quiz)$#i' => '\1zes',
            '#^(ox)$#i' => '\1en',
            '#([m|l])ouse$#i' => '\1ice',
            '#(matr|vert|ind)ix|ex$#i' => '\1ices',
            '#(x|ch|ss|sh)$#i' => '\1es',
            '#([^aeiouy]|qu)ies$#i' => '\1y',
            '#([^aeiouy]|qu)y$#i' => '\1ies',
            '#(hive)$#i' => '\1s',
            '#(?:([^f])fe|([lr])f)$#i' => '\1\2ves',
            '#sis$#i' => 'ses',
            '#([ti])um$#i' => '\1a',
            '#(buffal|tomat)o$#i' => '\1oes',
            '#(bu)s$#i' => '\1ses',
            '#(alias|status)#i' => '\1es',
            '#(octop|vir)us$#i' => '\1i',
            '#(ax|test)is$#i' => '\1es',
            '#s$#i' => 's',
            '#$#' => 's');

        $lowercasedWord = strtolower($word);

        foreach (Language::$uncountables as $uncountable) {
            if (substr($lowercasedWord, (-1 * strlen($uncountable))) == $uncountable) {
                return $word;
            }
        }

        foreach (Language::$irregulars as $plural => $singular) {
            if (preg_match('#(' . $plural . ')$#i', $word, $matches)) {
                return preg_replace('#(' . $plural . ')$#i', substr($matches[0], 0, 1) . substr($singular, 1), $word);
            }
        }

        foreach ($plurals as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return false;
    }

    /**
     * Singularizes English nouns.
     *
     * @param  string $word An English noun to singularize.
     * @return string A singular noun.
     */
    public static function singularize($word)
    {
        $singulars = array(
            '#(quiz)zes$#i' => '\1',
            '#(matr)ices$#i' => '\1ix',
            '#(vert|ind)ices$#i' => '\1ex',
            '#^(ox)en#i' => '\1',
            '#(alias|status)es$#i' => '\1',
            '#([octop|vir])i$#i' => '\1us',
            '#(cris|ax|test)es$#i' => '\1is',
            '#(shoe)s$#i' => '\1',
            '#(o)es$#i' => '\1',
            '#(bus)es$#i' => '\1',
            '#([m|l])ice$#i' => '\1ouse',
            '#(x|ch|ss|sh)es$#i' => '\1',
            '#(m)ovies$#i' => '\1ovie',
            '#(s)eries$#i' => '\1eries',
            '#([^aeiouy]|qu)ies$#i' => '\1y',
            '#([lr])ves$#i' => '\1f',
            '#(tive)s$#i' => '\1',
            '#(hive)s$#i' => '\1',
            '#([^f])ves$#i' => '\1fe',
            '#(^analy)ses$#i' => '\1sis',
            '#((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$#i' => '\1\2sis',
            '#([ti])a$#i' => '\1um',
            '#(n)ews$#i' => '\1ews',
            '#s$#i' => '',
        );

        $lowercasedWord = strtolower($word);
        foreach (Language::$uncountables as $uncountable) {
            if (substr($lowercasedWord, -1 * strlen($uncountable)) == $uncountable) {
                return $word;
            }
        }

        foreach (Language::$irregulars as $plural => $singular) {
            if (preg_match('#(' . $singular . ')$#i', $word, $matches)) {
                return preg_replace('#(' . $singular . ')$#i', substr($matches[0], 0, 1) . substr($plural, 1), $word);
            }
        }

        foreach ($singulars as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * Converts a phrase into a title.
     *
     * @param  string $phrase Word to format as tile
     * @return string Text formatted as title
     */
    public static function titleize($phrase)
    {
        $words = Language::getWords($phrase);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }

        return implode(' ', $words);
    }

    /**
     * Comverts an underscored phrase to camel casing.

     * @param  string $phrase A phrase to convert to camel case.
     * @return string An camelized phrase.
     */
    public static function camelize($phrase, $separator)
    {
        $words = Language::getWords($phrase);
        foreach ($words as &$word) {
            $word = ucfirst($word);
        }

        return implode('', $words);
    }

    /**
     * Converts phrase into an underscored phrase.
     *
     * @param  string $phrase A phrase to underscore.
     * @return string An underscored phrase.
     */
    public static function underscore($phrase)
    {
        return strtolower(implode('_', Language::getWords($phrase)));
    }

    /**
     * Converts a phrase into a human readable sentence.
     *
     * @param  string $phrase A phrase to humanize.
     * @return string A human readable phrase.
     */
    public static function humanize($phrase)
    {
        return Language::capitalize(implode(' ', Language::getWords($phrase)));
    }

    /**
     * Camelizes the phrase, then lowercases the first letter.
     *
     * @param  string $phrase A phrase to variablize.
     * @return string Returns a variablized phrase.
     */
    public static function variablize($phrase)
    {
        $phrase = Language::camelize($phrase);

        return strtolower($phrase[0]) . substr($phrase, 1);
    }

    /**
     * Converts a class name to its table name by underscoring and pluralizing
     * it.
     *
     * Converts "Person" to "people".
     *
     * @param  string $className A class name for getting the related table name.
     * @return string A plural table name.
     */
    public static function tableize($className)
    {
        return Language::pluralize(Language::underscore($className));
    }

    /**
     * Converts a table name to its class name by singularizing and camelizing
     * it.
     *
     * Converts "people" to "Person".
     *
     * @param  string $tableName A table name for getting the related class name.
     * @return string A singular class name.
     */
    public static function classify($tableName)
    {
        return Language::camelize(Language::singularize($tableName));
    }

    /**
     * Converts number to its ordinal English number.
     *
     * @param  integer $number A number to get its ordinal value.
     * @return string  The ordinal representation of given string.
     */
    public static function ordinalize($number)
    {
        if (in_array($number % 100, range(11, 13))) {
            return $number . 'th';
        }
        switch ($number % 10) {
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
        }

        return $number . 'th';
    }

    /**
     * Converts a phrase into an array of words.
     *
     * @param  string   $phrase A phrase to get words.
     * @return string[] An array of words from the given phrase.
     */
    public static function getWords($phrase)
    {
        return preg_split('#[^A-Za-z]|(?=[A-Z])#', $phrase, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function capitalize($word)
    {
        if (isset($word[0])) {
            return strtoupper($word[0]) . substr($word, 1);
        }

        return $word;
    }

}
