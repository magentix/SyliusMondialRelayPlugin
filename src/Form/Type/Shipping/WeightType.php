<?php
/**
 * @author    Matthieu Vion
 * @copyright 2018 Magentix
 * @license   https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 * @link      https://github.com/magentix/mondial-relay-plugin
 */
declare(strict_types = 1);

namespace Magentix\SyliusMondialRelayPlugin\Form\Type\Shipping;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WeightType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromValue', null, [
                'label' => 'mondial_relay.form.shipping_method.weight_from',
            ])
            ->add('toValue', null, [
                'label' => 'mondial_relay.form.shipping_method.weight_to',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                if (isset($data['amount'])) {
                    $data['amount'] /= 100;
                }
                $form->add('amount', null, [
                    'label' => 'mondial_relay.form.shipping_method.amount',
                    'data' => $data['amount'],
                ]);
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options): void {
                $eventData = $event->getData();
                $eventData['amount'] *= 100;
                $event->setData($eventData);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_shipping_calculator_mondial_relay_weight_range';
    }
}
