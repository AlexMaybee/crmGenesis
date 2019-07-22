<div class="plan_fact">

<div class="title">
    <!-- <h4 class="title_main">ПЛАН / ФАКТ </h4>-->
    <h4 class="title_main"><?=$arResult['NOW_DATE'];?></h4>
    <!-- <span class="title_date">(<?/*=$arResult['NOW_DATE'];*/?>)</span>-->
</div>

<?php
if(!$arResult['ERROR_EMTY_DATA']):
    ?>

    <div class="company_block">
        <div class="progress_bar_block">

            <div class="main_progressbar_visible">
                <div class="progress_title">
                    Общий план на месяц
                </div>
                <div class="progress_bar_wrapper">
                    <!--<div class="progress_bar_inner_border">
                                <div class="progress_bar_inner" style="width:<?/*=$arResult['COMPANY']['COMPLETED'].'%'*/?>">
                                    <div class="progress_bar_value"><?/*=$arResult['COMPANY']['COMPLETED'].'%'*/?></div>
                                </div>
                            </div>-->

                    <? if($arResult['COMPANY']['COMPLETED'] <= 100):?>
                        <div class="progress"  title="Получено <?=$arResult['COMPANY']['FACT'].' грн.'?>">
                            <?php
                            for($i = 0; $i < date('t',strtotime('now')); $i++):
                                ?>
                                <div class="progress-bar progress-days-absolute" style="width: <?=(100/date('t')).'%'?>; <? if(($i+1) == date('d')) echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" <? if(($i+1) == date('d')) echo 'title="'.'На '.date('d.m.Y').' выполнено '.$arResult['COMPANY']['COMPLETED'].'% плана'.'"'?>><span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span></div>
                            <?
                            endfor;
                            ?>

                            <div class="progress-bar" role="progressbar" style="width: <?=$arResult['COMPANY']['COMPLETED'].'%'?>; background-color:#15c4e0;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($arResult['COMPANY']['COMPLETED'] > 5) echo $arResult['COMPANY']['COMPLETED'].'%'?></div>
                            <!--   <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" style="width: <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$data['PLAN_COMPLETED']) > 5) echo (100-$data['PLAN_COMPLETED']).'%'*/?></div>-->
                        </div>
                    <? else: ?>
                        <div class="progress"  title="Получено <?=$arResult['COMPANY']['FACT'].' грн.'?>">

                            <?php
                            for($i = 0; $i < date('t',strtotime('now')); $i++):
                                ?>
                                <div class="progress-bar progress-days-absolute" style="width: <?=(100/date('t')).'%'?>; background-color:transparent; <? if(($i+1) == date('d')) echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" <? if(($i+1) == date('d')) echo 'title="'.date('d.m.Y').'"'?>><span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span></div>
                            <?
                            endfor;
                            ?>

                            <div class="progress-bar" role="progressbar" style="width: 100%;background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                            <div class="progress-bar" role="progressbar" title="Перевыполнение  <?=($arResult['COMPANY']['COMPLETED'] - 100).'%'?>" style="width: <?=($data['PLAN_COMPLETED'] - 100).'%'?>;background-color:#9f1ae6e0;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($arResult['COMPANY']['COMPLETED'] - 100).'%'?></div>
                        </div>
                    <?endif;?>
                </div>
                <div class="progress_bar_plan">
                    факт / план - <span class="progress_bar_plan_value"><?=$arResult['COMPANY']['FACT'].' / '.$arResult['COMPANY']['PLAN'].' грн.'?> </span>
                </div>
            </div>

        </div>

    </div>

    <div class="integrators_block">
        <h4 class="title">Интеграторы</h4>
        <?
        foreach ($arResult['INTEGRATORS'] as $id => $data):?>

            <div class="progress_bar_block">
                <div class="integrators_progressbar_visible">
                    <div class="progress_title">
                        <div class="progress_title_image_wrapper">
                            <img src="<?=$data['PHOTO']?>">
                        </div>
                        <div class="progress_title_data_wrapper">
                            <div class="employee_name"><?=$data['NAME']?></div>
                            <div class="employee_position"><?=$data['POSITION']?></div>
                            <div class="employee_hours_works" style="color: <?=$data['HOURS_WORKED']['COLOR']?>"><?=$data['HOURS_WORKED']['VALUE']?> ч. отработано в текущем месяце</div>
                        </div>
                    </div>
                    <div class="progress_bar_wrapper">

                        <? if($data['PLAN_COMPLETED'] <= 100):?>
                            <div class="progress"  title="Выполнено <?=$data['PLAN_COMPLETED'].'%'?>">
                                <div class="progress-bar" role="progressbar" style="width: <?=$data['PLAN_COMPLETED'].'%'?>; background-color:#15c4e0;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($data['PLAN_COMPLETED'] > 5) echo $data['PLAN_COMPLETED'].'%'?></div>
                                <!--   <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" style="width: <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$data['PLAN_COMPLETED']) > 5) echo (100-$data['PLAN_COMPLETED']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress"  title="Выполнено <?=$data['PLAN_COMPLETED'].'%'?>">
                                <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #48ad06" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar " role="progressbar" title="Перевыполнение  <?=($data['PLAN_COMPLETED'] - 100).'%'?>" style="width: <?=($data['PLAN_COMPLETED'] - 100).'%'?>; background-color: #9f1ae6e0;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($data['PLAN_COMPLETED'] - 100).'%'?></div>
                            </div>
                        <?endif;?>


                    </div>
                    <div class="progress_bar_plan">
                        факт / план  - <span class="progress_bar_plan_value"> <?=$data['DEALS_SUM']?> / <?=$data['MONTH_PLAN']?> грн.</span>
                    </div>
                </div>

                <div class="integrators_progressbar_hidden">

                    <?foreach ($data['CATEGORIES'] as $categoryData):?>

                        <div class="category_deal_progressbar_hidden">
                            <div class="category_deal_progressbar_hidden_title">
                                <?=$categoryData['NAME'];?>
                            </div>

                            <? if($categoryData['PERCENT_TO_EXISTED_SUM'] <= 100):?>
                                <div class="progress" title="Выполнено <?=$categoryData['PERCENT_TO_EXISTED_SUM'].'%'?>">
                                    <div class="progress-bar" role="progressbar" style="width: <?=$categoryData['PERCENT_TO_EXISTED_SUM'].'%'?>; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($categoryData['PERCENT_TO_EXISTED_SUM'] > 5) echo $categoryData['PERCENT_TO_EXISTED_SUM'].'%'?></div>
                                    <!--   <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$categoryData['PERCENT_TO_EXISTED_SUM']).'%'*/?>" style="width: <?/*=(100-$categoryData['PERCENT_TO_EXISTED_SUM']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$categoryData['PERCENT_TO_EXISTED_SUM']) > 5) echo (100-$categoryData['PERCENT_TO_EXISTED_SUM']).'%'*/?></div>-->
                                </div>
                            <? else: ?>
                                <div class="progress" title="Выполнено <?=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'?>">
                                    <!--<div class="progress-bar bg-info" role="progressbar" title="Выполнено <?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?>" style="width: <?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?></div>
                                    <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?>" style="width: <?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?></div>-->
                                    <div class="progress-bar" role="progressbar"  style="width: 100%; background-color:#9f1ae6e0;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                    <!-- <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?/*=($categoryData['PERCENT_TO_EXISTED_SUM'] - 100).'%'*/?>" style="width: <?/*=($categoryData['PERCENT_TO_EXISTED_SUM'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($categoryData['PERCENT_TO_EXISTED_SUM'] - 100).'%'*/?></div>-->
                                </div>
                            <?endif;?>

                            <div class="category_deal_progressbar_hidden_data">сумма : <?=$categoryData['SUM'].' грн.'?></div>
                        </div>

                    <?endforeach;?>

                </div>

            </div>

        <?endforeach;?>

    </div>
<? else:?>
    <div class="error_wrapper">
        <div class="error_emty_data">
            <span><?=$arResult['ERROR_EMTY_DATA']?></span>
        </div>
    </div>
<?endif?>



<div class="proggers">
    <h4 class="title">Проггеры, загрузка</h4>
    <?//Выводим прогеров?>
    <?foreach ($arResult['PROGGERS_V2'] as $id => $data):

        //не выводим проггеров, у которых сумма часов планам(HOURS_PROGGER_PLAN) == 0 и если он не выполнял работ (HOURS_PROGGER_FACT) == 0
        if($data['HOURS_PROGGER_PLAN'] > 0):
            ?>


            <div class="progress_bar_block">
                <div class="integrators_progressbar_visible">
                    <div class="progress_title">
                        <div class="progress_title_image_wrapper">
                            <img src="<?=$data['IMAGE_PATH']?>">
                        </div>
                        <div class="progress_title_data_wrapper">
                            <div class="employee_name"><?=$data['NAME']?></div>
                            <div class="employee_position"><?=$data['POSITION']?></div>
                            <div class="employee_hours_works" title="В текущем месяце отработано <?=$data['HOURS_PROGGER_FACT_CUR_MONTH']['VALUE']?> часов" style="color: <?=$data['HOURS_PROGGER_FACT_CUR_MONTH']['COLOR']?>"><?=$data['HOURS_PROGGER_FACT_CUR_MONTH']['VALUE']?> ч. отработано в текущем месяце</div>
                        </div>
                    </div>
                    <div class="progress_bar_wrapper">
                        <!--  <div class="progress_bar_inner_border">-->
                        <!-- <div class="progress_bar_inner" style="width:<?/*=$data['DEALS_SUM']/100000 * 100*/?>%">
                                <div class="progress_bar_value"><?/*=$data['DEALS_SUM']/100000 * 100*/?>%</div>
                            </div>-->

                        <? if($data['HOURS_PROGGER_PERCENT']['VALUE'] <= 100):?>
                            <!--<div class="progress" title="Загружен на <?/*=$data['HOURS_PROGGER_PERCENT']['VALUE'].'%'*/?>">
                                    <div class="progress-bar" role="progressbar" style="width: <?/*=$data['HOURS_PROGGER_PERCENT']['VALUE'].'%; background-color: '.$data['HOURS_PROGGER_PERCENT']['COLOR']*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if($data['HOURS_PROGGER_PERCENT']['VALUE'] > 5) echo $data['HOURS_PROGGER_PERCENT']['VALUE'].'%'*/?></div>-->
                            <div class="progress" title="Загружен на <?=($data['HOURS_PROGGER_PLAN'] - $data['HOURS_PROGGER_FACT']).' часов';?>">
                                <div class="progress-bar" role="progressbar" style="width: <?=$data['HOURS_PROGGER_PERCENT']['VALUE'].'%; background-color: '.$data['HOURS_PROGGER_PERCENT']['COLOR']?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($data['HOURS_PROGGER_PERCENT']['VALUE'] > 5) echo ($data['HOURS_PROGGER_PLAN'] - $data['HOURS_PROGGER_FACT']).' часов'; ?></div>
                                <!--   <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" style="width: <?/*=(100-$data['PLAN_COMPLETED']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$data['PLAN_COMPLETED']) > 5) echo (100-$data['PLAN_COMPLETED']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress" title="Перегружен на <?=($data['HOURS_PROGGER_PERCENT']['VALUE'] - 100).'%'?>">
                                <div class="progress-bar" role="progressbar" style="width: 100%;<?'background-color: '.$data['HOURS_PROGGER_PERCENT']['COLOR']?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar" role="progressbar" title="Перегружен на  <?=($data['HOURS_PROGGER_PERCENT']['VALUE'] - 100).'%'?>" style="width: <?=($data['HOURS_PROGGER_PERCENT']['VALUE'] - 100).'%'?>;background-color:#ee2210;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($data['HOURS_PROGGER_PERCENT']['VALUE'] - 100).'%'?></div>
                            </div>
                        <?endif;?>

                        <!--</div>-->

                    </div>
                    <div class="progress_bar_plan">
                        факт / план (часы) - <span class="progress_bar_plan_value"> <?=$data['HOURS_PROGGER_FACT']?> / <?=$data['HOURS_PROGGER_PLAN']?> </span>
                    </div>
                </div>

                <div class="integrators_progressbar_hidden">

                    <?
                    if(count($data['DEALS']) > 0):
                        foreach ($data['DEALS'] as $dealData):
                            //скрываем из списка сделок проггера те, у которых план == 0
                            if($dealData['DEAL_PLAN'] == 0) continue;
                            ?>

                            <div class="category_deal_progressbar_hidden">
                                <div class="category_deal_progressbar_hidden_title">
                                    <a href="/crm/deal/details/<?=$dealData['ID']?>/">
                                        <?=$dealData['TITLE'];?>
                                    </a>
                                </div>

                                <? if($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] < 100):?>
                                    <div class="progress" title="Выполнено на <?=$dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'].'% из '.$dealData['DEAL_PLAN'].' часов'; ?>">
                                        <div class="progress-bar" role="progressbar" style="width: <?=$dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'].'%'?>; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] > 5) echo $dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'].'%'?></div>
                                    </div>
                                <? else: //не понятно работает ли! ?>
                                    <div class="progress" title="Превышение на <?=($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] - 100).'% из '.$dealData['DEAL_PLAN'].' часов'; ?>">

                                        <div class="progress-bar" role="progressbar"  style="width: 100%; background-color:#9f1ae6e0;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                        <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?=($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] - 100).'%'?>" style="width: <?=($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($dealData['PROGGER_HOURS_CURRENT_MONTH_PERCENT'] - 100).'%'?></div>
                                    </div>

                                <?endif;?>

                                <div class="category_deal_progressbar_hidden_data"><?=$dealData['DEAL_FACT_BY_ELEMENTS'].' из '.$dealData['DEAL_PLAN'].' часов';?></div>
                            </div>

                        <?
                        endforeach;
                    endif;
                    ?>

                </div>

            </div>


        <?
        endif;
    endforeach
    ?>

</div>

</div>