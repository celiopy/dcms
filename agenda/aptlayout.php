<?php
    $user_id = User::is_logged_in($db);

    $types = [
        0 => 'Agendado', 
        1 => 'Encaixe', 
        2 => 'Orçamento', 
        6 => 'Lead', 
        3 => 'Emergência', 
        4 => 'Revisão', 
        7 => 'Revisão Semestral', 
        8 => 'Revisão Anual', 
        5 => 'Trabalho'
    ];
?>

<form method="POST" action="<?= BASEURL; ?>agenda/appointment.php?action=<?= ($event ? 'update&id=' . $event['id'] : 'add'); ?>" autocomplete="off">
    <fieldset<?= ($event && $event['FinishedIn'] ? ' disabled' : '');?>>
        <div class="flex flex-column gap-200">
            <div class="field">
                <input name="event[PatientID]" class="indexed" value="<?= $event ? $event['patient_id'] : ''; ?>" required>
                <input id="patient" type="text" value="<?= $event ? $event['patient_name'] : ''; ?>" data-find>
                <label for="patient" style="font-size: 1.2rem;" class="mi mi-user"></label>
                <ul id="result"></ul>
            </div>
            <div class="field">
                <input name="event[DoctorID]" class="indexed" value="<?= $event ? $event['doctor_id'] : $_GET['doctor']; ?>" required>
                <input type="text" value="<?= $event ? $event['doctor_name'] : Doctor::get($db, $_GET['doctor'], 'Name'); ?>" readonly>
                <label style="font-size: 1.2rem;" class="mi mi-doctor"></label>
            </div>
            <div class="field">
                <?php $uid = $event ? $event['UserID'] : $user_id; ?>
                <input type="text" value="<?= User::get($db, $uid, 'Name'); ?>" readonly>
                <label style="font-size: 1.2rem;" class="mi mi-agent"></label>
            </div>
            <div class="field | flex gap-200 items-center">
                <input type="date" name="event[DT]" value="<?= $event ? $event['DT'] : $_GET['date']; ?>" required>
                <label style="font-size: 1.2rem;" class="mi mi-time"></label>
                <div class="field | flex items-center">
                    <input type="time" name="event['Start']" value="<?= $event ? $event['Start'] : $_GET['time']; ?>" required>
                </div>
                <div class="mi mi-trending"></div>
                <div class="field | flex items-center">
                    <input type="time" name="event['End']" value="<?= $event ? $event['End'] : DateTime::createFromFormat('H:i', $_GET['time'])->add(new DateInterval('PT30M'))->format('H:i'); ?>" required>
                </div>
            </div>
            <div class="flex gap-200 items-center">
            </div>
            <div class="field">
                <select name="event[Ext]" id="" required>
                    <option value="" selected disabled>Tipo</option>
                    <?php foreach ($types as $k => $v) { ?>
                        <option value="<?= $k; ?>"<?= ( $event && $k === $event['Ext'] ? ' selected' : '' ); ?>><?= $v; ?>
                    <?php } ?>
                </select>
                <label style="font-size: 1.2rem;" class="mi mi-label-2-match"></label>
            </div>
            <?php if ($event) { ?>
            <div class="field">
                <select name="event[Status]" id="" required>
                    <option value="1" <?= (! $event['CheckIn'] &&! $event['FinishedIn']) ? ' selected' : ''; ?>>Agendado</option>
                    <option value="2" <?= ($event['CheckIn'] &&! $event['AttendIn']) ? ' selected' : ''; ?>>Chegou</option>
                    <option value="3" <?= ($event['CheckIn'] && $event['AttendIn']) ? ' selected' : ''; ?>>Em atendimento</option>
                    <option value="4" <?= ($event['CheckIn'] && $event['FinishedIn']) ? ' selected' : ''; ?>>Terminado</option>
                    <option value="5" <?= (! $event['CheckIn'] && $event['FinishedIn']) ? ' selected' : ''; ?>>Faltou</option>
                </select>
                <label style="font-size: 1.2rem;" class="mi mi-timelapse"></label>
            </div>
            <?php } ?>
            <div class="flex gap-200 items-center">
                <?php if ($event) { ?>
                    <button type="submit" class="btn-success" title="Salvar mudanças">Salvar</button>
                    <button type="button" class="btn-drop | icon mi mi-delete" title="Excluir entrada"></button>
                <?php } else { ?>
                    <button type="submit" class="btn-success" title="Novo agendamento">Criar</button>
                <?php } ?>
            </div>
            <?php if ($event) { ?>
            <div></div>
            <details>
                <summary>#<?= $event['id']; ?> - Criado em <?= $event['CreatedAt']; ?></summary>
                <div>
                    <ul role="list">
                        <li>Atendido em <?= $event['AttendIn']; ?></li>
                        <li>Finalizado em <?= $event['FinishedIn']; ?></li>
                        <li><?php
                            $date1 = new DateTime($event['AttendIn']);
                            $date2 = new DateTime($event['FinishedIn']);
                            $interval = $date1->diff($date2);
                            $minutes = $interval->i + ($interval->h * 60);
                            echo "<i class=\"mi mi-time\"></i> Duração: $minutes minuto" . ( $minutes === 1 ? '' : 's' ) . ".";
                        ?></li>
                    </ul>
                </div>
            </details>
            <?php } ?>
        </div>
    </fieldset>
</form>
<style>
details > summary {
    cursor: pointer;
    padding: .25rem;
    background-color: var(--clr-deep);
}

details > div {
    padding: .25rem 0.5rem;
    background-color: var(--clr-deep);
}
</style>