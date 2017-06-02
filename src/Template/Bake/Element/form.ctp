<%

use Cake\Utility\Inflector;

$fields = collection($fields)
->filter(function($field) use ($schema) {
return $schema->columnType($field) !== 'binary';
});

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
$fields = $fields->reject(function ($field) {
return $field === 'lft' || $field === 'rght';
});
}
%>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2><%= $pluralHumanName %></h2>
        <ol class="breadcrumb">
            <li><%= $pluralHumanName %></li>
            <li class="active">
                <strong><% if (strpos($action, 'add') === false): %>
                    Editar <%= $pluralHumanName %>
                    <% else: %>
                    Cadastrar <%= $pluralHumanName %>
                    <% endif; %></strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="<%= $pluralVar %> form">
                        <?= $this->Form->create($<%= $singularVar %>) ?>
                        <fieldset>
                            <% if (strpos($action, 'add') === false): %>
                            <legend><?= __('Editar <%= $singularHumanName %>') ?></legend>
                            <% else: %>
                            <legend><?= __('Cadastrar <%= $singularHumanName %>') ?></legend>
                            <% endif; %>
                            <?php
                            <%
                            foreach ($fields as $field) {
                                if (in_array($field, $primaryKey)) {
                                    continue;
                                }
                                if (isset($keyFields[$field])) {
                                    $fieldData = $schema->column($field);
                                    if($field != 'situacao_id' && $field != 'user_id') {
                                        if (!empty($fieldData['null'])) {
                                            %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'empty' => true]);
                                            echo "</div>";
                                            <%
                                        } else {
                                            %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>]);
                                            echo "</div>";
                                            <%
                                        }
                                    }
                                    continue;
                                }
                                if (!in_array($field, ['created', 'modified', 'updated'])) {
                                    $fieldData = $schema->column($field);
                                    if (($fieldData['type'] === 'date') && (!empty($fieldData['null']))) {
                                        if($fieldData['type'] === 'date'){
                                            %>
                                            echo "<div class='col-md-6'>";
                                            <% if (strpos($action, 'add') === false): %>
                                                echo $this->Form->input('<%= $field %>', ['empty' => true,'type'=>'text','class'=>'datepicker','value'=>$this->Time->format($<%= $singularVar %>-><%= $field %>,'dd-MM-Y')]);
   <% else: %>
                                                echo $this->Form->input('<%= $field %>', ['empty' => true,'type'=>'text','class'=>'datepicker']);
                                                <% endif; %>
                                            echo "</div>";
                                            <% }elseif($fieldData['type'] === 'datetime'){ %>
                                            echo "<div class='col-md-6'>";
                                            <% if (strpos($action, 'add') === true): %>
                                            echo $this->Form->input('<%= $field %>', ['empty' => true,'type'=>'text','class'=>'datetimepicker']);
   <% else: %>
                                            echo $this->Form->input('<%= $field %>', ['empty' => true,'type'=>'text','class'=>'datetimepicker','value'=>$this->Time->format($<%= $singularVar %>-><%= $field %>,'dd/MM/Y HH:mm')]);
                                            <% endif; %>
                                                echo "</div>";
<% }else{ %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>', ['empty' => true]);
                                            echo "</div>";

                                            <%        }
                                    } else {
                                        if($fieldData['type'] === 'date') {
                                            %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>', ['type' => 'text', 'class' => 'datepicker','value'=>$this->Time->format($<%= $singularVar %>-><%= $field %>,'dd-MM-Y')]);
                echo "</div>";
<%
            }elseif($fieldData['type'] === 'datetime'){
                                            %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>', ['type' => 'text', 'class' => 'datetimepicker','value'=>$this->Time->format($<%= $singularVar %>-><%= $field %>,'dd-MM-Y H:m:ss')]);
                echo "</div>";
<%
            }else{
                                            %>
                                            echo "<div class='col-md-6'>";
                                            echo $this->Form->input('<%= $field %>');
                                            echo "</div>";
                                            <%           }
                                    }
                                }
                            }
                            if (!empty($associations['BelongsToMany'])) {
                                foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
                                    %>
                                    echo "<div class='col-md-6'>";
                                    echo $this->Form->input('<%= $assocData['property'] %>._ids', ['options' => $<%= $assocData['variable'] %>]);
                echo "</div>";
<%
            }
                            }
                            %>
                            ?>

                        </fieldset>
                        <div class="col-md-12 text-right">
                            <?= $this->Form->submit(__('Salvar'),['class'=>'btn btn-primary']) ?>
                        </div>
                        <div class="clearfix"></div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

