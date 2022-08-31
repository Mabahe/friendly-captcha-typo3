<?php
declare(strict_types=1);

namespace BalatD\FriendlyCaptcha\Domain\Validator\SpamShield;

use BalatD\FriendlyCaptcha\Services\FriendlyCaptchaService;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Validator\SpamShield\AbstractMethod;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

/**
 * Class FriendlyCaptchaMethod
 */
class FriendlyCaptchaMethod extends AbstractMethod
{

    /**
     * @return bool true if spam recognized
     * @throws Exception
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function spamCheck(): bool
    {
        if (!$this->isFormWithFriendlycaptchaField() || $this->isCaptchaCheckToSkip()) {
            return false;
        }
        $friendlyCaptcha = GeneralUtility::getContainer()->get(FriendlyCaptchaService::class);
        $status = $friendlyCaptcha->validateFriendlyCaptcha();
        if ($status['verified'] === true && (string)$status['errors'] === '') {
            return false;
        }
        return true;
    }

    /**
     * Check if current form has a recaptcha field
     *
     * @return bool
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws Exception
     */
    protected function isFormWithFriendlycaptchaField(): bool
    {
        foreach ($this->mail->getForm()->getPages() as $page) {
            /** @var Field $field */
            foreach ($page->getFields() as $field) {
                if ($field->getType() === 'friendlycaptcha') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Captcha check should be skipped on createAction if there was a confirmationAction where the captcha was
     * already checked before
     * Note: $this->flexForm is only available in powermail 3.9 or newer
     *
     * @return bool
     */
    protected function isCaptchaCheckToSkip(): bool
    {
        if (property_exists($this, 'flexForm')) {
            $confirmationActive = $this->flexForm['settings']['flexform']['main']['confirmation'] === '1';
            return $this->getActionName() === 'create' && $confirmationActive;
        }
        return false;
    }

    /**
     * @return string "confirmation" or "create"
     */
    protected function getActionName(): string
    {
        $pluginVariables = GeneralUtility::_GPmerged('tx_powermail_pi1');
        return $pluginVariables['action'];
    }
}
