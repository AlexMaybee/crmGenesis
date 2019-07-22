
<div class="block-wrapper company" v-if="company">

    <div class="block-main">
        <block-hitle
                :title="company.DATA"
        ></block-hitle>

        <block-rogress
                :percent="company.COMPANY_COMPLETED"
                :plan="company.MONTH_PLAN"
                :fact="company.MONTH_FACT"
        ></block-rogress>

        <block-bumbers
                v-bind:plan="company.MONTH_PLAN"
                v-bind:fact="company.MONTH_FACT"
                v-bind:measure-code="'грн.'">
        </block-bumbers>
    </div>

    <transition name="subStatistics">
        <div class="block-sub mb-30">
            <div class="block-sub-elem">
                <block-hitle
                        :title="'Выставлено счетов на сумму'"
                ></block-hitle>

                <block-rogress
                        v-bind:percent="(company.INVOICES_POTENCIAL / company.MONTH_PLAN) * 100"
                        v-bind:plan="company.MONTH_PLAN"
                        v-bind:fact="company.INVOICES_POTENCIAL"
                ></block-rogress>

                <block-bumbers
                        v-bind:plan="company.MONTH_PLAN"
                        v-bind:fact="company.INVOICES_POTENCIAL"
                        v-bind:measure-code="'грн.'">
                </block-bumbers>
            </div>
        </div>
    </transition>

</div>



<div class="block-wrapper analitics"
     v-for="user in users">
    <?/*@mouseover="showHiddenAnalitics = !showHiddenAnalitics"
     @mouseout="showHiddenAnalitics = !showHiddenAnalitics"*/?>

<div class="block-main">
    <block-hitle
        :photo="user.PHOTO"
        :photo-title="user.NAME"
        :user-name="user.NAME"
        :user-position="user.WORK_POSITION"
        :user-hours= "user.HOURS_WORKED"
    ></block-hitle>

    <block-rogress
        v-bind:percent="user.PLAN_COMPLETED"
        v-bind:plan="user.MONTH_PLAN"
        v-bind:fact="user.MONTH_FACT"
    ></block-rogress>

    <block-bumbers
        v-bind:plan="user.MONTH_PLAN"
        v-bind:fact="user.MONTH_FACT"
        v-bind:measure-code="'грн.'">
    </block-bumbers>
</div>


<transition name="subStatistics">
    <div class="block-sub mb-30">
        <div class="block-sub-elem">
            <block-hitle
                :title="'Выставлено счетов на сумму'"
            ></block-hitle>

            <block-rogress
                v-bind:percent="(user.INVOICES_POTENCIAL / user.MONTH_PLAN) * 100"
                v-bind:plan="user.MONTH_PLAN"
                v-bind:fact="user.INVOICES_POTENCIAL"
            ></block-rogress>

            <block-bumbers
                v-bind:plan="user.MONTH_PLAN"
                v-bind:fact="user.INVOICES_POTENCIAL"
                v-bind:measure-code="'грн.'">
            </block-bumbers>
        </div>
    </div>
</transition>

<transition name="subStatistics">

    <div class="block-sub" <?//v-show="showHiddenAnalitics"?> v-if="user.CATEGORIES">
        <div class="block-sub-elem" v-for="category in user.CATEGORIES" v-if="category.INVOICES_SUM > 0">
            <block-hitle
                :title="category.NAME"
            ></block-hitle>

            <block-rogress
                v-bind:percent="category.INVOICES_SUM / user.MONTH_FACT * 100"
                v-bind:plan="user.MONTH_FACT"
                v-bind:fact="category.INVOICES_SUM"
            ></block-rogress>

            <block-bumbers
                v-bind:plan="user.MONTH_FACT"
                v-bind:fact="category.INVOICES_SUM"
                v-bind:measure-code="'грн.'">>>
            </block-bumbers>
        </div>
    </div>
</transition>
</div>