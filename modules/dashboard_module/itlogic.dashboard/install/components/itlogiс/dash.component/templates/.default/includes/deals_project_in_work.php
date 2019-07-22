<div class="projects_in_work">
    <h4 class="title">Проекты в работе (<?=count($arResult['PROJECTS_IN_WORK']).' шт.'?>)</h4>

    <div class="projects_in_realization_block">

        <?
        foreach ($arResult['PROJECTS_IN_WORK'] as $project):
            ?>

            <div class="projects_progressbar_block">
                <div class="projects_progressbar_visible">
                    <div class="projects_progressbar_title_wrapper">

                        <div class="projects_progressbar_title_value">
                            <div class="projects_progressbar_assigned_by_img_wrapper" title="<?=$project['ASSIGNED_BY_NAME']?>">
                                <img src="<?=$project['ASSIGNED_BY_IMG_PATH']?>">
                            </div>
                            <a href="/crm/deal/details/<?=$project['ID']?>/"><?='#'.$project['ID'].' '.$project['TITLE']?></a>

                            <div class="projects_progressbar_title_hours_panned" style="color: <?=$project['AVARGE_HOURS_PLAN_MINUS_FACT']['COLOR']?>">
                                ( <? echo ($project['AVARGE_HOURS_PLAN_MINUS_FACT']['VALUE'] >= 0) ? 'осталось: '.$project['AVARGE_HOURS_PLAN_MINUS_FACT']['VALUE'] : 'превышение на '.(-$project['AVARGE_HOURS_PLAN_MINUS_FACT']['VALUE']) ?> ч. )
                            </div>
                        </div>

                        <div class="projects_progressbar_title_data">факт / план (часы) - <?=$project['AVARGE_HOURS_SUM_FACT']?> / <?=$project['AVARGE_HOURS_SUM_PLAN']?></div>
                    </div>

                    <div class="projects_progressbar_wrapper">

                        <? if($project['AVARGE_HOURS_PERCENT'] <= 100):?>
                            <div class="progress" title="Выполнено <?=$project['AVARGE_HOURS_PERCENT'].'%'?>">
                                <div class="progress-bar" role="progressbar" style="width: <?=$project['AVARGE_HOURS_PERCENT'].'%'?>; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($project['AVARGE_HOURS_PERCENT'] > 5) echo $project['AVARGE_HOURS_PERCENT'].'%'?></div>
                                <!-- <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$project['AVARGE_HOURS_PERCENT']).'%'*/?>" style="width: <?/*=(100-$project['AVARGE_HOURS_PERCENT']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$project['AVARGE_HOURS_PERCENT']) > 5) echo (100-$project['AVARGE_HOURS_PERCENT']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress">
                                <!--<div class="progress-bar bg-info" role="progressbar" title="Выполнено <?/*=$project['AVARGE_HOURS_PERCENT'].'%'*/?>" style="width: <?/*=$project['AVARGE_HOURS_PERCENT'].'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*=$project['AVARGE_HOURS_PERCENT'].'%'*/?></div>
                                    <div class="progress-bar bg-danger" role="progressbar" title="Попандос  <?/*=($project['AVARGE_HOURS_PERCENT'] - 100).'%'*/?>" style="width: <?/*=($project['AVARGE_HOURS_PERCENT'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($project['AVARGE_HOURS_PERCENT'] - 100).'%'*/?></div>-->
                                <div class="progress-bar" role="progressbar" title="Выполнено 100%" style="width: 100%; background-color:#48ad06;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar" role="progressbar" title="Попандос  <?=($project['AVARGE_HOURS_PERCENT'] - 100).'%'?>" style="width: <?=($project['AVARGE_HOURS_PERCENT'] - 100).'%'?>; background-color:#ee2210;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($project['AVARGE_HOURS_PERCENT'] - 100).'%'?></div>
                            </div>
                        <?endif;?>

                    </div>
                </div>
                <div class="projects_progressbar_hidden">
                    <div class="projects_progressbar_hidden_analitics">
                        <div class="projects_progressbar_hidden_title">
                            <div class="projects_progressbar_otdel">Аналитик's</div>
                            <div class="projects_progressbar_employees">

                                <?
                                $counter = 1;
                                $marginLeft = -20;
                                foreach ($project['EMPLOYEES']['ANALITICS'] as $id => $val):?>
                                    <img src="<?=$val['IMAGE_PATH']?>" title="<?=$val['NAME']?>" <? if($counter > 1){ echo 'style="margin-left: '.$marginLeft.'px"'; } echo 'element-count="'.$counter.'"'?>>
                                    <?
                                    $counter++;
                                endforeach; ?>

                            </div>
                        </div>

                        <? if($project['AVARGE_HOURS_ANALITIC_PERCENT'] <= 100):?>
                            <div class="progress" title="Выполнено <?=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'?>">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($project['AVARGE_HOURS_ANALITIC_PERCENT'] > 5) echo $project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'?></div>
                                <!--  <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$project['AVARGE_HOURS_ANALITIC_PERCENT']).'%'*/?>" style="width: <?/*=(100-$project['AVARGE_HOURS_ANALITIC_PERCENT']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$project['AVARGE_HOURS_ANALITIC_PERCENT']) > 5) echo (100-$project['AVARGE_HOURS_ANALITIC_PERCENT']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress" title="Недооценка на  <?=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'?>">
                                <!--<div class="progress-bar bg-info" role="progressbar" title="Выполнено <?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?>" style="width: <?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*=$project['AVARGE_HOURS_ANALITIC_PERCENT'].'%'*/?></div>
                                    <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?>" style="width: <?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'*/?></div>-->
                                <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($project['AVARGE_HOURS_ANALITIC_PERCENT'] - 100).'%'?></div>
                            </div>
                        <?endif;?>

                        <div class="projets_progressbar_hidden_data">факт / план (часы) - <?=$project['UF_CRM_1529754702']?> / <?=$project['UF_CRM_1529754646']?></div>
                    </div>
                    <div class="projects_progressbar_hidden_programmers">
                        <div class="projects_progressbar_hidden_title">
                            <div class="projects_progressbar_otdel">Программер's</div>
                            <div class="projects_progressbar_employees">

                                <?
                                $counter = 1;
                                $marginLeft = -20;
                                foreach ($project['EMPLOYEES']['PROGGERS'] as $id => $val):?>
                                    <img src="<?=$val['IMAGE_PATH']?>" title="<?=$val['NAME']?>" <? if($counter > 1){ echo 'style="margin-left: '.$marginLeft.'px"'; } echo 'element-count="'.$counter.'"'?>>
                                    <?
                                    $counter++;
                                endforeach; ?>

                            </div>
                        </div>

                        <? if($project['AVARGE_HOURS_PROGGER_PERCENT'] <= 100):?>
                            <div class="progress" title="Выполнено <?=$project['AVARGE_HOURS_PROGGER_PERCENT'].'%'?>">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?=$project['AVARGE_HOURS_PROGGER_PERCENT'].'%'?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($project['AVARGE_HOURS_PROGGER_PERCENT'] > 5) echo $project['AVARGE_HOURS_PROGGER_PERCENT'].'%'?></div>
                                <!-- <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$project['AVARGE_HOURS_PROGGER_PERCENT']).'%'*/?>" style="width: <?/*=(100-$project['AVARGE_HOURS_PROGGER_PERCENT']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$project['AVARGE_HOURS_PROGGER_PERCENT']) > 5) echo (100-$project['AVARGE_HOURS_PROGGER_PERCENT']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress" title="Недооценка на  <?=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'?>">
                                <!--<div class="progress-bar bg-info" role="progressbar" title="Выполнено <?/*=$project['AVARGE_HOURS_PROGGER_PERCENT'].'%'*/?>" style="width: <?/*=$project['AVARGE_HOURS_PROGGER_PERCENT'].'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*=$project['AVARGE_HOURS_PROGGER_PERCENT'].'%'*/?></div>
                                    <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?/*=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'*/?>" style="width: <?/*=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'*/?></div>-->
                                <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($project['AVARGE_HOURS_PROGGER_PERCENT'] - 100).'%'?></div>
                            </div>
                        <?endif;?>

                        <div class="projets_progressbar_hidden_data">факт / план (часы) - <?=$project['UF_CRM_1529755353']?> / <?=$project['UF_CRM_1529755279']?></div>
                    </div>
                    <div class="projects_progressbar_hidden_ocenka">
                        <div class="projects_progressbar_hidden_title">
                            <div class="projects_progressbar_otdel">Оценка's</div>
                            <div class="projects_progressbar_employees">

                                <?
                                $counter = 1;
                                $marginLeft = -20;
                                foreach ($project['EMPLOYEES']['OCENKA'] as $id => $val):?>
                                    <img src="<?=$val['IMAGE_PATH']?>" title="<?=$val['NAME']?>" <? if($counter > 1){ echo 'style="margin-left: '.$marginLeft.'px"'; } echo 'element-count="'.$counter.'"'?>>
                                    <?
                                    $counter++;
                                endforeach; ?>

                            </div>
                        </div>

                        <? if($project['AVARGE_HOURS_OCENKA_PERCENT'] <= 100):?>
                            <div class="progress" title="Выполнено <?=$project['AVARGE_HOURS_OCENKA_PERCENT'].'%'?>">
                                <div class="progress-bar bg-info" role="progressbar"  style="width: <?=$project['AVARGE_HOURS_OCENKA_PERCENT'].'%'?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?if($project['AVARGE_HOURS_OCENKA_PERCENT'] > 5) echo $project['AVARGE_HOURS_OCENKA_PERCENT'].'%'?></div>
                                <!--    <div class="progress-bar bg-success" role="progressbar" title="Осталось <?/*=(100-$project['AVARGE_HOURS_OCENKA_PERCENT']).'%'*/?>" style="width: <?/*=(100-$project['AVARGE_HOURS_OCENKA_PERCENT']).'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*if((100-$project['AVARGE_HOURS_OCENKA_PERCENT']) > 5) echo (100-$project['AVARGE_HOURS_OCENKA_PERCENT']).'%'*/?></div>-->
                            </div>
                        <? else: ?>
                            <div class="progress" title="Недооценка на  <?=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'?>">
                                <!--<div class="progress-bar bg-info" role="progressbar" title="Выполнено <?/*=$project['AVARGE_HOURS_OCENKA_PERCENT'].'%'*/?>" style="width: <?/*=$project['AVARGE_HOURS_OCENKA_PERCENT'].'%'*/?>" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><?/*=$project['AVARGE_HOURS_OCENKA_PERCENT'].'%'*/?></div>
                                    <div class="progress-bar bg-warning" role="progressbar" title="Попандос  <?/*=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'*/?>" style="width: <?/*=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'*/?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?/*=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'*/?></div>-->
                                <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">100%</div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'?>" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"><?=($project['AVARGE_HOURS_OCENKA_PERCENT'] - 100).'%'?></div>
                            </div>
                        <?endif;?>

                        <div class="projets_progressbar_hidden_data">факт / план (часы) - <?=$project['UF_CRM_1529755333']?> / <?=$project['UF_CRM_1529755307']?></div>
                    </div>
                </div>
            </div>

        <? endforeach;?>



    </div>

</div>