<?php

namespace Slim\View;

use Slim\View;

class FenomAdapter extends View
{

    /**
     * Настройки Fenom по-умолчанию.
     * https://github.com/fenom-template/fenom/blob/master/docs/ru/configuration.md
     * @var array
     */
    private $fenomConfig = [

        /**
         * Задает имя каталога, в котором хранятся компилированные
         * шаблоны. По умолчанию это sys_get_temp_dir().
         */
        'cache_path' => null,

        /**
         * Отключает возможность вызова методов в шаблоне.
         */
        'disable_methods' => false,

        /**
         * Отключает возможность использования фунций PHP, за исключением разрешенных.
         */
        'disable_native_funcs' => false,

        /**
         * Отключает возможность вызова статических методов и функций в шаблоне.
         */
        'disable_php_calls' => false,

        /**
         * Автоматически пересобирать кеш шаблона если шаблон изменился.
         * Понижает производительность.
         */
        'auto_reload' => true,

        /**
         * Каждый раз пересобирать кеш шаблонов (рекоммендуется только для отладки).
         * Очень сильно понижает производительность.
         */
        'force_compile' => false,

        /**
         * Не кешировать компилированный шаблон.
         * Эпично понижает производительность.
         */
        'disable_cache' => false,

        /**
         * Стараться, по возможности, вставить код дочернего шаблона в родительский
         * при подключении шаблона. Повышает производительность, увеличивает размер
         * файлов в кеше, уменьшает количество файлов в кеше.
         */
        'force_include' => true,

        /**
         * Автоматически экранировать HTML сущности при выводе переменных в шаблон.
         * Понижает производительность.
         */
        'auto_escape' => true,

        /**
         * Автоматически проверять существование переменной перед использованием в шаблоне.
         * Понижает производительность.
         */
        'force_verify' => false,

        /**
         * Удаляет лишние пробелы в шаблоне. Уменьшает размер кеша.
         */
        'strip' => false
    ];

    private $fenomCompileDir = null;

    public function __construct($fenomConfig = [])
    {
        parent::__construct();

        $this->fenomConfig = array_merge($this->fenomConfig, $fenomConfig);
        $this->fenomCompileDir = $this->fenomConfig['cache_path'];
        unset($this->fenomConfig['cache_path']);

        if (empty($this->fenomCompileDir))
        {
            $this->fenomCompileDir = sys_get_temp_dir();
        }
    }

    private $fenomInstance = null;

    private function getFenomSingleton()
    {
        if (is_null($this->fenomInstance))
        {
            $this->fenomInstance = \Fenom::factory(
                $this->getTemplatesDirectory(),
                $this->fenomCompileDir,
                $this->fenomConfig
            );
        }

        return $this->fenomInstance;
    }

    public function setTemplatesDirectory($directory)
    {
        parent::setTemplatesDirectory($directory);

        $this->fenomInstance = null;
    }

    public function render($template, $data = null)
    {
        $data = array_merge($this->data->all(), (array)$data);
        $render = $this->getFenomSingleton()->fetch($template, $data);

        return $render;
    }
}