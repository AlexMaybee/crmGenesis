
<div class="block-wrapper deals-in-work"
    v-for="deal in deals_on_realization">
    <div class="deal_main_block">
        <div class="block-deal-info mb-30">
            <deal-title-component
                    :title="'#' + deal.ID + ' ' + deal.TITLE"
                    :photo="deal.ASSIGNED_BY_PHOTO" alt="Assigne BY Photo"
                    :photo-title="deal.ASSIGNED_BY_NAME"
                    :link="'/crm/deal/details/' + deal.ID + '/'"
                    :time-rest="(deal.AVARGE_DEAL_PLAN - deal.AVARGE_DEAL_FACT).toFixed(2)"
                    :plan="deal.AVARGE_DEAL_PLAN"
                    :fact="deal.AVARGE_DEAL_FACT"
                    :measure-code="'час.'"
            ></deal-title-component>
        </div>
        <div class="block-deal-progress">
            <block-rogress
                    :percent="deal.AVARGE_DEAL_FACT / deal.AVARGE_DEAL_PLAN * 100"
                    :plan="deal.AVARGE_DEAL_PLAN"
                    :fact="deal.AVARGE_DEAL_FACT"
            ></block-rogress>
        </div>
    </div>
    <div class="deal_sub_block"
        v-for="role in deal.ROLES">

        <deal-role-title
                :title="role.TITLE"
        >

            <img v-for="user in role.EMPLOYEES" :src="user.IMAGE_PATH" alt="user photo" :title="user.NAME">


        </deal-role-title>

        <block-rogress
                :percent="role.FACT / role.PLAN * 100"
                :plan="role.PLAN"
                :fact="role.FACT"
        ></block-rogress>

        <block-bumbers
                v-bind:plan="role.PLAN"
                v-bind:fact="role.FACT"
                v-bind:measure-code="'час.'">
        </block-bumbers>


    </div>


</div>