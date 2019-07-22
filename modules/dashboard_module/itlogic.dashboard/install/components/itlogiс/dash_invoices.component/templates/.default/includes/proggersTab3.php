
<div class="block-wrapper proggers"
     v-for="progger in proggers">

    <div class="block-main">
        <block-hitle
            :photo="progger.PHOTO"
            :photo-title="progger.NAME"
            :user-name="progger.NAME"
            :user-position="progger.WORK_POSITION"
            :user-hours= "progger.HOURS_WORKED"
        ></block-hitle>

        <block-rogress
            v-bind:percent="(progger.HOURS_WHOLE.FACT) / (progger.HOURS_WHOLE.PLAN) * 100"
            v-bind:plan="progger.HOURS_WHOLE.PLAN"
            v-bind:fact="progger.HOURS_WHOLE.FACT"
        ></block-rogress>

        <block-bumbers
            v-bind:plan="progger.HOURS_WHOLE.PLAN"
            v-bind:fact="progger.HOURS_WHOLE.FACT"
            v-bind:measure-code="'hours'">
        </block-bumbers>
    </div>

    <div class="block-sub-2" v-if="progger.ROLES.length > 0">
        <span>РОЛИ:</span>
        <transition name="subStatistics">
            <div class="block-sub mb-30">
                <div class="block-sub-elem"
                     v-for="roles in progger.ROLES"
                     v-if="roles.FACT > 0">
                    
                    <block-hitle
                        :title="roles.TITLE"
                    ></block-hitle>

                    <block-rogress
                        v-bind:percent="(roles.FACT / roles.PLAN) * 100"
                        v-bind:plan="roles.PLAN"
                        v-bind:fact="roles.FACT"
                    ></block-rogress>

                    <block-bumbers
                        v-bind:plan="roles.PLAN"
                        v-bind:fact="roles.FACT"
                        v-bind:measure-code="'hours'">>
                    </block-bumbers>
                </div>
            </div>
        </transition>
    </div>

    <div class="block-sub-2" v-if="progger.DEALS.length > 0">
        <span>СДЕЛКИ:</span>
        <transition name="subStatistics">
            <div class="block-sub mb-30">
                <div class="block-sub-elem"
                     v-for="deal in progger.DEALS">

                    <block-hitle
                        :title="deal.TITLE"
                        :text-link="'/crm/deal/details/' + deal.ID + '/' "
                        :photo="deal.ASSIGNED_BY_PHOTO"
                        :photo-title=" 'Ответственный ' + deal.ASSIGNED_BY_NAME"
                    ></block-hitle>

                    <block-rogress
                        v-bind:percent="(deal.DEAL_HOURS_FACT_WHOLE / deal.DEAL_HOURS_PLAN_WHOLE) * 100"
                        v-bind:plan="deal.DEAL_HOURS_PLAN_WHOLE"
                        v-bind:fact="deal.DEAL_HOURS_FACT_WHOLE"
                    ></block-rogress>

                    <block-bumbers
                        v-bind:plan="deal.DEAL_HOURS_PLAN_WHOLE"
                        v-bind:fact="deal.DEAL_HOURS_FACT_WHOLE"
                        v-bind:measure-code="'hours'">>
                    </block-bumbers>
                </div>
            </div>
        </transition>
    </div>


</div>