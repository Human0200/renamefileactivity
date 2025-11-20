<tr>
  <td align="right" width="40%"><span class="adm-required-field">Новое расширение</span>:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("string", 'extension', $arCurrentValues['extension'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td align="right" width="40%"><span class="adm-required-field">ID файла</span>:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("int", 'fileid', $arCurrentValues['fileid'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td align="right" width="40%"><span class="adm-required-field">Базовое имя файла</span>:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("string", 'newfilename', $arCurrentValues['newfilename'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td align="right" width="40%">Номер документа:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("string", 'documentnumber', $arCurrentValues['documentnumber'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td align="right" width="40%">Дата документа:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("string", 'documentdate', $arCurrentValues['documentdate'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td align="right" width="40%">Контрагент:</td>
  <td width="60%">
    <?= CBPDocument::ShowParameterField("string", 'contractorname', $arCurrentValues['contractorname'], ['size' => '50']) ?>
  </td>
</tr>
<tr>
  <td colspan="2">
    <div style="background: #f0f0f0; padding: 10px; margin-top: 10px; border-radius: 3px;">
      <b>Пример результата:</b><br>
      Счет №123 от 01.01.2024 ООО Рога и Копыта.pdf
    </div>
  </td>
</tr>