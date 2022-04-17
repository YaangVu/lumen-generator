<?php
/**
 * @Author yaangvu
 * @Date   Jan 19, 2022
 */

namespace YaangVu\LumenGenerator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class NamespaceGenerator
{
    static protected array $commandReplacements
        = [
            'Console', 'Controller', 'Service', 'Events', 'Exception', 'Request', 'Jobs', 'Listeners', 'Mail',
            'Middleware', 'Pipe', 'Model', 'Policy', 'Provider', 'Serve', 'Test', 'Resource', 'Notification',
            'NotificationTable', 'Channel', 'SchemaDump', 'Cast', 'Rule', 'Factory'
        ];

    static string $rootNamespace = 'Domains\\';

    /**
     * @Description  Generate namespace by name and type
     *
     * @Author       yaangvu
     * @Date         Jan 19, 2022
     *
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    static function generateNamespace(string $name, string $type): string
    {
        $arrName = self::parseNameInput($name);

        return self::$rootNamespace . $arrName['first'] . '\\' . Str::plural(Str::studly($type));
    }

    /**
     * @Description  Generate full namespace by name and type
     *
     * @Author       yaangvu
     * @Date         Jan 19, 2022
     *
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    static function generateFullNamespace(string $name, string $type): string
    {
        $arrName = self::parseNameInput($name);

        return self::$rootNamespace . $arrName['first'] . '\\'
            . Str::plural(Str::studly($type)) . '\\' . $arrName['last']
            . ($type != 'Model' ? Str::studly($type) : '');
    }

    /**
     * @Description Generate class name by name and type
     *
     * @Author      yaangvu
     * @Date        Jan 19, 2022
     *
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    static function generateClass(string $name, string $type): string
    {
        $arrName = self::parseNameInput($name);

        return $arrName['last'] . ($type != 'Model' ? Str::studly($type) : '');
    }

    /**
     * @Description get path of class by name
     *
     * @Author      yaangvu
     * @Date        Jan 19, 2022
     *
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    static function getPath(string $name, string $type): string
    {
        $arrName = self::parseNameInput($name);
        $type    = Str::studly($type);

        $path = Str::lower(self::$rootNamespace) . $arrName['first'] . '\\'
            . Str::plural($type) . '\\' . $arrName['last']
            . ($type != 'Model' ? $type : '')
            . '.php';

        return str_replace('\\', '/', $path);
    }

    /**
     * @Description Parse Name input to array
     *
     * @Author      yaangvu
     * @Date        Jan 17, 2022
     *
     * @param string $name
     *
     * @return array
     */
    #[ArrayShape(['first' => "mixed", 'last' => "mixed", 'hasSub' => "bool", 'subLevel' => "int"])]
    static function parseNameInput(string $name): array
    {
        $name    = self::replaceRootNamespace($name);
        $arrName = explode('\\', $name);

        return [
            'first'    => Str::studly(str_replace(self::$commandReplacements, '', Arr::first($arrName))),
            'last'     => Str::studly(Arr::last($arrName)),
            'hasSub'   => count($arrName) > 1,
            'subLevel' => count($arrName)
        ];
    }

    /**
     * @Description Replace root namespace default
     *
     * @Author      yaangvu
     * @Date        Jan 19, 2022
     *
     * @param string $name
     *
     * @return string
     */
    static function replaceRootNamespace(string $name): string
    {
        $name = str_replace('/', '\\', $name);
        $name = str_replace([self::$rootNamespace, ...self::$commandReplacements], '', $name);

        return trim($name, '\\/');
    }
}