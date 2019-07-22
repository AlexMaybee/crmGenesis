

<!--<div class="block-wrapper invoices">
    <div class="block-main">
        <block-hitle-lists
        :test1="'LOLLOLOLLOLLO!'">
        </block-hitle-lists>
    </div>
</div>-->

<div class="block-wrapper invoices" v-if="unpayed_invoices">
    <div class="block-main">
        <block-hitle
                :title="'Выставлено счетов на сумму на ' + company.DATA"
        ></block-hitle>

        <block-rogress
                :percent="unpayed_invoices.INVOICES_POTENCIAL / company.MONTH_PLAN * 100"
                :plan="company.MONTH_PLAN"
                :fact="unpayed_invoices.INVOICES_POTENCIAL"
        ></block-rogress>

        <block-bumbers
                v-bind:plan="company.MONTH_PLAN"
                v-bind:fact="unpayed_invoices.INVOICES_POTENCIAL"
                v-bind:measure-code="'грн.'">
        </block-bumbers>
    </div>

</div>


<div class="block-wrapper invoices" v-if="unpayed_invoices.DEALS_WITH_INVOICES.length > 0"
     v-for="deal in unpayed_invoices.DEALS_WITH_INVOICES">

    <div class="block-main" >

        <block-hitle
                :title="'Сделка #' + deal.ID + ' - ' + deal.TITLE"
                :text-link="'/crm/deal/details/' + deal.ID + '/'"
                :photo="deal.ASSIGNED_BY_PHOTO"
                :photo-title=" 'Ответственный ' + deal.ASSIGNED_BY_NAME"
                :vzyatka="deal.VZYATKA"
        ></block-hitle>

        <block-rogress
                v-bind:percent="(deal.INVOICES_PAYED / deal.OPPORTUNITY) * 100"
                v-bind:plan="deal.OPPORTUNITY"
                v-bind:fact="deal.INVOICES_PAYED"
        ></block-rogress>

        <block-bumbers
                v-bind:plan="deal.OPPORTUNITY"
                v-bind:fact="deal.INVOICES_PAYED"
                v-bind:measure-code="'грн.'">
        </block-bumbers>

    </div>

    <transition name="subStatistics">
        <div class="block-sub mb-30">
            <div class="block-sub-elem">
                <block-hitle
                        :title="'Выставлено счетов на сумму сделки'"
                ></block-hitle>

                <block-rogress
                        v-bind:percent="(deal.INVOICES_POTENCIAL / deal.OPPORTUNITY) * 100"
                        v-bind:plan="deal.OPPORTUNITY"
                        v-bind:fact="deal.INVOICES_POTENCIAL"
                ></block-rogress>

                <block-bumbers
                        v-bind:plan="deal.OPPORTUNITY"
                        v-bind:fact="deal.INVOICES_POTENCIAL"
                        v-bind:measure-code="'грн.'">
                </block-bumbers>
            </div>
        </div>
    </transition>

    <div class="block-sub-2">
        <transition name="subStatistics">
            <div class="block-sub mb-30">

                <block-hitle-lists>

                    <div v-for="invoice in deal.INVOICES">
                        <block-list-elements
                                             :title="'#' + invoice.ID + ' - ' + invoice.ORDER_TOPIC"
                                             :link="'/crm/invoice/show/' + invoice.ID + '/' "
                                             :status="invoice.STATUS_NAME"
                                             :photo="invoice.RESPONSIBLE_PHOTO"
                                             :photo-title=" 'Ответственный ' + invoice.RESPONSIBLE_NAME"
                                             :price="invoice.PRICE + ' грн.'"
                        ></block-list-elements>
                    </div>


                </block-hitle-lists>
                </div>
        </transition>
    </div>


    <div class="block-sub-elem"
         v-if="deal.INVOICES.length > 0"
        >

            <!--<block-hitle
                    :title="'Счет #' + invoice.ID + ' - ' + invoice.ORDER_TOPIC"
                    :text-link="'/crm/invoice/show/' + invoice.ID + '/' "
                    :status="invoice.STATUS_NAME"
                    :photo="invoice.RESPONSIBLE_PHOTO"
                    :photo-title=" 'Ответственный ' + invoice.RESPONSIBLE_NAME"
            ></block-hitle>-->




           <!-- <block-rogress
                    v-bind:percent="(invoice.PRICE / deal.OPPORTUNITY) * 100"
                    v-bind:plan="deal.OPPORTUNITY"
                    v-bind:fact="invoice.PRICE"
            ></block-rogress>

            <block-bumbers
                    v-bind:plan="deal.OPPORTUNITY"
                    v-bind:fact="invoice.PRICE"
                    v-bind:measure-code="'грн.'">>
            </block-bumbers>
-->
    </div>

</div>