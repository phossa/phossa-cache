<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Package
 * @package   Phossa\Cache
 * @author    Hong Zhang <phossa@126.com>
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
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
 *     $encrypt = new Extension\EncryptExtension([
 *           'encrypt'   => 'my_encrypt_function',
 *           'decrypt'   => 'my_decrypt_function'
 *     ]);
 *
 *     // enable encryption
 *     $cache->setExtension($encrypt);
 * </code>
 *
 * @package Phossa\Cache
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Cache\Extension\ExtensionAbstract
 * @version 1.0.8
 * @since   1.0.0 added
 */
class EncryptExtension extends ExtensionAbstract
{
    /**
     * encrypt callable, signature `function (string): string {}`
     *
     * @var    callable
     * @access protected
     */
    protected $encrypt = 'base64_encode';

    /**
     * decrypt callable, signature `function (string): string {}`
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
        return [
            ExtensionStage::STAGE_POST_GET     => 40,
            ExtensionStage::STAGE_PRE_SAVE     => 70,
            ExtensionStage::STAGE_PRE_DEFER    => 70
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
        if ($item instanceof CacheItemInterface) {
            if ($stage === ExtensionStage::STAGE_POST_GET) {
                if ($item->isHit()) {
                    $fnc = $this->decrypt;
                    $res = $fnc($item->get());
                }
            } else {
                $fnc = $this->encrypt;
                $res = $fnc($item->get());
            }
        }

        if (isset($res)) {
            // encrypt/decrypt failed
            if ($res === false) {
                return $this->falseAndSetError(
                    Message::get(
                        Message::CACHE_FAIL_ENCRYPT,
                        $item->getKey()
                    ),
                    Message::CACHE_FAIL_ENCRYPT
                );
            }

            // set to new string value
            $item->set($res);
        }

        return true;
    }
}
