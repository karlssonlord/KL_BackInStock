# KL_BackInStock
You get the ability to (ajax) request stock alert notifications for individual associated products + you don't have to be logged in to be able to subscribe.


## Usage
In your /template/catalog/product/view.phtml template just add

    <?php echo $this->getChildHtml('back_in_stock_cta') ?>

...where you want it.

The modal is placed here atm:

    <default>
        <reference name="after_body_start">
            <block type="backinstock/modal" name="back.in.stock.modal" template="backinstock/modal.phtml"/>
        </reference>
    </default>

...but should probably be placed only on the product page, rather than globally.

##TODO

1. Add modal only on product pages
2. ...