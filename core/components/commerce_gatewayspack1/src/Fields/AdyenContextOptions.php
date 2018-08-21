<?php

namespace modmore\Commerce\GatewaysPack1\Fields;

use modmore\Commerce\Admin\Widgets\Form\Field;

class AdyenContextOptions extends Field
{
    public function isValidValue($value)
    {
        return is_array($value);
    }

    public function getHTML()
    {
        $c = $this->adapter->newQuery('modContext');
        $c->where([
            'key:!=' => 'mgr',
        ]);
        $c->sortby('name', 'ASC');
        $ctxs = [];
        foreach ($this->adapter->getIterator('modContext', $c) as $context) {
            $ctxs[] = $context->get(['key', 'name']);
        }
        return $this->commerce->twig->render('adyenhpp/fields/contextoptions.twig', ['field' => $this, 'contexts' => $ctxs]);
    }

    public function getValueAsArray()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            return $value;
        }
        if ($array = json_decode($value, true)) {
            return $array;
        }
        return [];
    }
}