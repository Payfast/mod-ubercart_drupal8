<?php
/**
 * Copyright (c) 2008 PayFast (Pty) Ltd
 * You (being anyone who is not PayFast (Pty) Ltd) may download and use this plugin / code in your own website in conjunction with a registered and active PayFast account. If your PayFast account is terminated for any reason, you may not use this plugin / code or part thereof.
 * Except as expressly indicated in this licence, you may not use, copy, modify or distribute this plugin / code or part thereof in any way.
 */

namespace Drupal\uc_payfast\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\uc_order\OrderInterface;

/**
 * Returns the form for the custom Review Payment screen for Express Checkout.
 */
class PayFastReviewForm extends FormBase {

  /**
   * The order that is being reviewed.
   *
   * @var \Drupal\uc_order\OrderInterface
   */
  protected $order;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uc_payfast_review_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, OrderInterface $order = NULL) {
    $this->order = $order;
    $form = \Drupal::service('plugin.manager.uc_payment.method')
      ->createFromOrder($this->order)
      ->getExpressReviewForm($form, $form_state, $this->order);

    if (empty($form)) {
      \Drupal::service('session')->set('uc_checkout_review_' . $this->order->id(), TRUE);
      return $this->redirect('uc_cart.checkout_review');
    }

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Continue checkout'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::service('plugin.manager.uc_payment.method')
      ->createFromOrder($this->order)
      ->submitExpressReviewForm($form, $form_state, $this->order);

    \Drupal::service('session')->set('uc_checkout_review_' . $this->order->id(), TRUE);
    $form_state->setRedirect('uc_cart.checkout_review');
  }

}
