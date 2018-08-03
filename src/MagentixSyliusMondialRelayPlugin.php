<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types=1);

namespace Magentix\SyliusMondialRelayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class MagentixSyliusMondialRelayPlugin extends Bundle
{
    use SyliusPluginTrait;
}
