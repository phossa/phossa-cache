<?php
/*
 * Phossa Project
 *
 * @see         http://www.phossa.com/
 * @copyright   Copyright (c) 2015 phossa.com
 * @license     http://mit-license.org/ MIT License
 */
/*# declare(strict_types=1); */

namespace Phossa\Cache\Extension;

use Phossa\Cache\CachePoolInterface;
use Phossa\Cache\CacheItemInterface;
use Phossa\Cache\Message\Message;

/**
 * A simple encryption extension.
 *
 * This extension will encrypt the serialized item before save to cache and
 * will decrypt the item before unserialization.
 *
 * <code>
 *     $cache->setExtensions([
 *         // set encrypt/decrypt callable
 *         [ 'className' => 'EncryptExtension',
 *           'encrypt'   => 'my_encrypt_function',
 *           'decrypt'   => 'my_decrypt_function'
 *         ]
 *     ]);
 * </code>
 *
 * @package \Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class EncryptExtension extends ExtensionAbstract
{
    /**
     * encrypt callable
     *
     * @var    callable
     * @access protected
     */
    protected $encrypt = 'base64_encode';

    /**
     * decrypt callable
     *
     * @var    callable
     * @access protected
     */
    protected $decrypt = 'base64_decode';

    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [ ExtensionStage::STAGE_POST_GET     => 40,
                 ExtensionStage::STAGE_PRE_SAVE     => 60,
                 ExtensionStage::STAGE_PRE_DEFER    => 60
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        CachePoolInterface $cache,
        /*# string */ $stage,
        CacheItemInterface $item = null
    )/*# : bool */ {
        if ($stage === ExtensionStage::STAGE_POST_GET) {
            if ($item->isHit()) {
                $fnc = $this->decrypt;
                $res = $fnc($item->get());
            }
        } else {
            $fnc = $this->encrypt;
            $res = $fnc($item->get());
        }

        if (isset($res)) {
            if ($res === false) {
                return $this->falseAndSetError(
                    Message::get(
                        Message::CACHE_FAIL_ENCRYPT, $item->getKey()
                    ),
                    Message::CACHE_FAIL_ENCRYPT
                );
            }
            $item->set($res);
        }
        return true;
    }
}
