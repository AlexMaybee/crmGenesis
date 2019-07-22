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
                        Общий план в разрезе счетов
                    </div>
                    <div class="progress_bar_wrapper">

                        <? if($arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'] <= 100):?>
                            <div class="progress"  title="Получено <?=$arResult['COMPANY']['INVOICES_PAYED_SUM'].' грн.'?>">
                                <?php
                                for($i = 0; $i < date('t',strtotime('now')); $i++):
                                    ?>
                                    <div class="progress-bar progress-days-absolute" style="width: <?=(100/date('t')).'%'?>;
                                    <? if(($i+1) == date('d')) echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>"
                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                        <? if(($i+1) == date('d')) echo 'title="'.'На '.date('d.m.Y').' выполнено '.$arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'].'% плана'.'"'?>>
                                        <span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span>
                                    </div>
                                <?
                                endfor;
                                ?>

                                <div class="progress-bar" role="progressbar"
                                     style="width: <?=$arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'].'%'?>; background-color:#15c4e0;"
                                     aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                    <?if($arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'] > 5) echo $arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'].'%'?>
                                </div>
                            </div>
                        <? else: ?>
                            <div class="progress"  title="Получено <?=$arResult['COMPANY']['INVOICES_PAYED_SUM'].' грн.'?>">

                                <?php
                                for($i = 0; $i < date('t',strtotime('now')); $i++):
                                    ?>
                                    <div class="progress-bar progress-days-absolute" style="width: <?=(100/date('t')).'%'?>; background-color:transparent; <? if(($i+1) == date('d')) echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                        <? if(($i+1) == date('d')) echo 'title="'.date('d.m.Y').'"'?>>
                                        <span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span>
                                    </div>
                                <?
                                endfor;
                                ?>

                                <div class="progress-bar" role="progressbar" style="width: 100%;background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar" role="progressbar" title="Перевыполнение <?=($arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'] - 100).'%'?>"
                                     style="width: <?=($data['INVOICES_PAYED_IN_PERCENT'] - 100).'%'?>;background-color:#9f1ae6e0;" aria-valuenow="30"
                                     aria-valuemin="0" aria-valuemax="100"><?=($arResult['COMPANY']['INVOICES_PAYED_IN_PERCENT'] - 100).'%'?>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                    <div class="progress_bar_plan">
                        факт / план - <span class="progress_bar_plan_value"><?=$arResult['COMPANY']['INVOICES_PAYED_SUM'].' / '.$arResult['COMPANY']['PLAN'].' грн.'?> </span>
                    </div>
                </div>

            </div>


            <div class="progress_bar_block">

                <div class="main_progressbar_visible">
                    <div class="progress_title">
                        Потенциальный доход по неоплаченным счетам
                    </div>
                    <div class="progress_bar_wrapper">

                        <? if($arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'] <= 100):?>
                            <div class="progress"  title="Получено <?=$arResult['COMPANY']['POTENCIAL_INVOICES_SUM'].' грн.'?>">
                                <?php /*
                                for($i = 0; $i < date('t',strtotime('now')); $i++):
                                    ?>
                                    <div class="progress-bar progress-days-absolute" style="width: <?=(100/date('t')).'%'?>;
                                    <? if(($i+1) == date('d')) echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>"
                                         aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                                        <? if(($i+1) == date('d'))
                                            echo 'title="'.'При оплате всех счетов будет выполнено '.$arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'].'% плана'.'"'?>>
                                        <span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span>
                                    </div>
                                <?
                                endfor;
                                */?>

                                <div class="progress-bar" role="progressbar"
                                     style="width: <?=$arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'].'%'?>; background-color:#15c4e0;"
                                     aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                                    <?if($arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'] > 5) echo $arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'].'%'?>
                                </div>
                            </div>
                        <? else: ?>
                            <div class="progress"  title="Выставлено счетов на <?=$arResult['COMPANY']['POTENCIAL_INVOICES_SUM'].' грн.'?>">

                                <?php
                                for($i = 0; $i < date('t',strtotime('now')); $i++):
                                    ?>
                                    <div class="progress-bar progress-days-absolute"
                                         style="width: <?=(100/date('t')).'%'?>; background-color:transparent; <? if(($i+1) == date('d'))
                                             echo 'border-right: 3px solid #000;'?> left: <?=((100/date('t')) * $i).'%'?>" aria-valuenow="20"
                                         aria-valuemin="0" aria-valuemax="100"
                                        <? if(($i+1) == date('d')) echo 'title="'.date('d.m.Y').'"'?>>
                                        <span class="progress_day_of_month"><?=date('d',strtotime(($i+1).'.'.date('m.Y')))?></span>
                                    </div>
                                <?
                                endfor;
                                ?>

                                <div class="progress-bar" role="progressbar" style="width: 100%;background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar" role="progressbar" title="Перевыполнение <?=($arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'] - 100).'%'?>"
                                     style="width: <?=($data['POTENCIAL_INVOICES_IN_PERCENT'] - 100).'%'?>;background-color:#9f1ae6e0;" aria-valuenow="30"
                                     aria-valuemin="0" aria-valuemax="100"><?=($arResult['COMPANY']['POTENCIAL_INVOICES_IN_PERCENT'] - 100).'%'?>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                    <div class="progress_bar_plan">
                        ожидается / план - <span class="progress_bar_plan_value"><?=$arResult['COMPANY']['POTENCIAL_INVOICES_SUM'].' / '.$arResult['COMPANY']['PLAN'].' грн.'?> </span>
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
                            </div>
                        </div>
                        <div class="progress_bar_wrapper">

                            <? if($data['PLAN_COMPLETED_BY_INVOICES_PERCENT'] <= 100):?>
                                <div class="progress"  title="Выполнено <?=$data['PLAN_COMPLETED_BY_INVOICES_PERCENT'].'%'?>">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: <?=$data['PLAN_COMPLETED_BY_INVOICES_PERCENT'].'%'?>; background-color:#15c4e0;"
                                         aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($data['PLAN_COMPLETED_BY_INVOICES_PERCENT'] > 5)
                                             echo $data['PLAN_COMPLETED_BY_INVOICES_PERCENT'].'%'?></div>
                                </div>
                            <? else: ?>
                                <div class="progress"  title="Выполнено <?=$data['PLAN_COMPLETED_BY_INVOICES_PERCENT'].'%'?>">
                                    <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #48ad06" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                    <div class="progress-bar " role="progressbar" title="Перевыполнение  <?=($data['PLAN_COMPLETED_BY_INVOICES_PERCENT'] - 100).'%'?>"
                                         style="width: <?=($data['PLAN_COMPLETED_BY_INVOICES_PERCENT'] - 100).'%'?>; background-color: #9f1ae6e0;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">
                                        <?=($data['PLAN_COMPLETED_BY_INVOICES_PERCENT'] - 100).'%'?>
                                    </div>
                                </div>
                            <?endif;?>


                        </div>
                        <div class="progress_bar_plan">
                            получено / план  - <span class="progress_bar_plan_value"> <?=$data['PLAN_COMPLETED_BY_INVOICES']?> / <?=$data['MONTH_PLAN']?> грн.</span>
                        </div>
                    </div>




                    <div class="integrators_progressbar_visible">
                        <div class="progress_title">
                           Выставлено счетов на сумму
                        </div>
                        <div class="progress_bar_wrapper">

                            <? if($data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] <= 100):?>
                                <div class="progress"  title="Выполнено <?=$data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'].'%'?>">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: <?=$data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'].'%'?>; background-color:#15c4e0;" aria-valuenow="15" aria-valuemin="0"
                                         aria-valuemax="100"><?if($data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] > 5) echo $data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'].'%'?></div>
                                </div>
                            <? else: ?>
                                <div class="progress"  title="Выполнено <?=$data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'].'%'?>">
                                    <div class="progress-bar" role="progressbar" style="width: 100%; background-color: #48ad06" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                    <div class="progress-bar " role="progressbar" title="Перевыполнение  <?=($data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] - 100).'%'?>"
                                         style="width: <?=($data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] - 100).'%'?>; background-color: #9f1ae6e0;" aria-valuenow="30"
                                         aria-valuemin="0" aria-valuemax="100"><?=($data['PLAN_POTENCIAL_BY_INVOICES_PERCENT'] - 100).'%'?></div>
                                </div>
                            <?endif;?>


                        </div>
                        <div class="progress_bar_plan">
                            ожидается / план  - <span class="progress_bar_plan_value"> <?=$data['PLAN_POTENCIAL_BY_INVOICES']?> / <?=$data['MONTH_PLAN']?> грн.</span>
                        </div>
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

</div>

<?//12.06.2019 Сделки в на стадии оплаты?>
<?if(count($arResult['POTENCIAL_INVOICES']) > 0): ?>
    <div class="projects_in_work">
        <h4 class="title">неоплаченные счета по сделкам (<?=count($arResult['POTENCIAL_INVOICES_DEALS']).' шт.'?>)</h4>

        <div class="projects_in_realization_block">

        <?foreach ($arResult['POTENCIAL_INVOICES_DEALS'] as $deal):?>

            <div class="projects_progressbar_block">
                <div class="projects_progressbar_visible">

                    <div class="projects_progressbar_title_wrapper">

                        <div class="projects_progressbar_title_value">
                            <div class="projects_progressbar_assigned_by_img_wrapper" title="<?=$deal['ASSIGNED_BY_NAME']?>">
                                <img src="<?=$deal['ASSIGNED_BY_IMG_PATH']?>">
                            </div>
                            <a href="/crm/deal/details/<?=$deal['ID']?>/"><?='#'.$deal['ID'].' '.$deal['TITLE']?></a>

                        </div>

                        <div class="projects_progressbar_title_data">выставлено счетов на сумму / сумма сделки - <?=round($deal['INVOICES_WHOLE_SUM'],2)?> / <?=$deal['OPPORTUNITY']?></div>
                    </div>


                    <div style="padding: 0 20px">
                        <div class="">Счета:</div>
                        <div class="bills_title_wrapper">
                            <?// отображение счетов?>
                            <ul>
                                <?foreach ($deal['INVOICES'] as $invoice):?>

                                    <li class="projects_progressbar_title_value">
                                        <div class="projects_progressbar_assigned_by_img_wrapper" title="<?=$invoice['RESPONSIBLE_NAME']?>">
                                            <img src="<?=$invoice['RESPONSIBLE_IMG_PATH']?>">
                                        </div>
                                        <a href="/crm/invoice/show/<?=$invoice['ID']?>/"><?='#'.$invoice['ID'].' '.$invoice['ORDER_TOPIC']?></a> -
                                        <span class="invoice_price"><?=round($invoice['PRICE'],2).' грн.'?></span> -
                                        <span class="invoice_status" <?if($invoice['STATUS_ID'] == 'P') echo 'style="color: green"'?>><?=$invoice['STATUS_NAME']?></span>
                                    </li>

                                <?endforeach;?>
                            </ul>


                        </div>
                    </div>

                  <!--  <div class="projects_progressbar_wrapper">
                        <?/* if($deal['INVOICES_BILLED_PERCENT'] <= 100):*/?>
                            <div class="progress" title="Выставлено на <?/*=$deal['INVOICES_BILLED_PERCENT'].'% от суммы сделки'*/?>">
                                <div class="progress-bar" role="progressbar" style="width: <?/*=$deal['INVOICES_BILLED_PERCENT'].'%'*/?>; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if($deal['INVOICES_BILLED_PERCENT'] > 5) echo $deal['INVOICES_BILLED_PERCENT'].'%'*/?></div>
                            </div>
                        <?/* else: */?>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" title="Выполнено 100%"
                                     style="width: 100%; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar" role="progressbar"
                                     title="Попандос <?/*=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'*/?>"
                                     style="width: <?/*=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'*/?>; background-color:#ee2210;"
                                     aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'*/?></div>
                            </div>
                        <?/*endif;*/?>
                    </div>-->

                    <div class="projects_progressbar_hidden">

                        <?//Счетов выставлено
                        if($deal['INVOICES_WHOLE_SUM'] > 0):
                        ?>
                            <div class="projects_progressbar_hidden_analitics">
                                <div class="projects_progressbar_hidden_title">
                                    <div class="projects_progressbar_otdel">Выставлено счетов на сумму</div>
                                </div>

                                <? if($deal['INVOICES_BILLED_PERCENT'] < 100):?>
                                    <div class="progress" title="Выполнено <?=$deal['INVOICES_BILLED_PERCENT'].'%'?>">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: <?=$deal['INVOICES_BILLED_PERCENT'].'%'?>" aria-valuenow="15" aria-valuemin="0"
                                             aria-valuemax="100"><?if($deal['INVOICES_BILLED_PERCENT'] > 5) echo $deal['INVOICES_BILLED_PERCENT'].'%'?></div>
                                    </div>
                                <? else: ?>
                                    <div class="progress" title="Недооценка на  <?=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'?>">
                                        <div class="progress-bar bg-info" role="progressbar"
                                             style="width: 100%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                        <div class="progress-bar bg-danger" role="progressbar"
                                             style="width: <?=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0"
                                             aria-valuemax="100"><?=($deal['INVOICES_BILLED_PERCENT'] - 100).'%'?></div>
                                    </div>
                                <?endif;?>

                                <div class="projets_progressbar_hidden_data">выставлено / сумма сделки - <?=$deal['INVOICES_WHOLE_SUM']?> / <?=$deal['OPPORTUNITY']?></div>
                            </div>
                        <?endif;?>

                        <?//Счетов оплачено
                        if($deal['INVOICES_PAYED'] > 0):
                            ?>
                            <div class="projects_progressbar_hidden_analitics">
                                <div class="projects_progressbar_hidden_title">
                                    <div class="projects_progressbar_otdel">Оплачено из них</div>
                                </div>

                                <? if($deal['INVOICES_PAYED_PERCENT'] <= 100):?>
                                    <div class="progress" title="Выполнено <?=$deal['INVOICES_PAYED_PERCENT'].'%'?>">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: <?=$deal['INVOICES_PAYED_PERCENT'].'%'?>" aria-valuenow="15" aria-valuemin="0"
                                             aria-valuemax="100"><?if($deal['INVOICES_PAYED_PERCENT'] > 5) echo $deal['INVOICES_PAYED_PERCENT'].'%'?></div>
                                    </div>
                                <? else: ?>
                                    <div class="progress" title="Не оплачено на  <?=($deal['INVOICES_PAYED_PERCENT'] - 100).'%'?>">
                                        <div class="progress-bar bg-success" role="progressbar"
                                             style="width: 100%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: <?=($deal['INVOICES_PAYED_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0"
                                             aria-valuemax="100"><?=($deal['INVOICES_PAYED_PERCENT'] - 100).'%'?></div>
                                    </div>
                                <?endif;?>

                                <div class="projets_progressbar_hidden_data">оплачено / сумма сделки - <?=$deal['INVOICES_PAYED']?> / <?=$deal['OPPORTUNITY']?></div>
                            </div>
                        <?endif;?>




                    </div>

                </div>
            </div>

        <?endforeach;?>

        </div>
    </div>
<?endif;?>