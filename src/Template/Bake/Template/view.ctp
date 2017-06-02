<%

use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
%>


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2><%= $pluralHumanName %></h2>
        <ol class="breadcrumb">
            <li><%= $pluralHumanName %></li>
            <li class="active">
                <strong>Litagem de <%= $pluralHumanName %></strong>
            </li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <h3>Detalhes <%= $pluralHumanName %></h3>
                        <table class="table table-hover">
<% if ($groupedFields['string']) : %>
<% foreach ($groupedFields['string'] as $field) : %>
<% if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
%>
                            <tr>
                                <% if ($details['property']=='user') { %>
                                <th><%= "<%= __('Quem Cadastrou') %>" %></th>
                                <% }else if ($details['property']=='situacao_cadastro') { %>
                                <th><%= "<%= __('Situação Cadastro') %>" %></th>
                                <% }else  { %>
                                <th><?= __('<%= Inflector::humanize($details['property']) %>') ?></th>
                                <% } %>
                                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
                            </tr>
<% else : %>
                            <tr>
                                <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
                                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
                            </tr>
<% endif; %>
<% endforeach; %>
<% endif; %>
<% if ($associations['HasOne']) : %>
    <%- foreach ($associations['HasOne'] as $alias => $details) : %>
                            <tr>
                                <th><?= __('<%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>') ?></th>
                                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
                            </tr>
    <%- endforeach; %>
<% endif; %>
<% if ($groupedFields['number']) : %>
<% foreach ($groupedFields['number'] as $field) : %>
                            <tr>
                                <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
                                <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
                            </tr>
<% endforeach; %>
<% endif; %>
<% if ($groupedFields['date']) : %>
<% foreach ($groupedFields['date'] as $field) : %>
                            <tr>
                                <% if ($field=='created') { %>
                                <th><%= "<%= __('Dt. Criação') %>" %></th>
                                <% }else if ($field=='modified') { %>
                                <th><%= "<%= __('Dt. Modificação') %>" %></th>
                                <% }else  { %>
                                <th><%= "<%= __('" . Inflector::humanize($field) . "') %>" %></th>
                                <% } %>
                                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
                            </tr>
<% endforeach; %>
<% endif; %>
<% if ($groupedFields['boolean']) : %>
<% foreach ($groupedFields['boolean'] as $field) : %>
                            <tr>
                                <th><?= __('<%= Inflector::humanize($field) %>') ?>6</th>
                                <td><?= $<%= $singularVar %>-><%= $field %> ? __('Sim') : __('Não'); ?></td>
                            </tr>
<% endforeach; %>
<% endif; %>
                        </table>
                    </div>
                </div>
            </div>

<% if ($groupedFields['text']) : %>
<% foreach ($groupedFields['text'] as $field) : %>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3><?= __('<%= Inflector::humanize($field) %>') ?></h3>
                </div>
                <div class="ibox-content">
                    <?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?>
                </div>
            </div>
<% endforeach; %>
<% endif; %>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
    %>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h3><?= __('Related <%= $otherPluralHumanName %>') ?></h3>
        </div>
        <div class="ibox-content">
            <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                <tr>
    <% foreach ($details['fields'] as $field): %>
                    <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
    <% endforeach; %>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
                <tr>
                <%- foreach ($details['fields'] as $field): %>
                    <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
                <%- endforeach; %>
                <%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
                    <td class="actions">
                        <?= $this->Html->link($this->Html->icon('list-alt'), ['controller' => '<%= $details['controller'] %>','action' => 'view', <%= $otherPk %>],['data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Detalhes','escape' => false,'class'=>'btn btn-xs btn-info']) ?>
                        <?= $this->Html->link($this->Html->icon('pencil'), ['controller' => '<%= $details['controller'] %>','action' => 'edit', <%= $otherPk %>],['data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Editar','escape' => false,'class'=>'btn btn-xs btn-primary']) ?>
                        <?= $this->Form->postLink($this->Html->icon('remove'), ['controller' => '<%= $details['controller'] %>','action' => 'delete', <%= $otherPk %>], ['confirm' => __('Você tem certeza que deseja excluir o registro # {0}?', <%= $pk %>),'data-toggle'=>'tooltip','data-placement'=>'bottom','title'=>'Deletar','escape' => false,'class'=>'btn btn-xs btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
<% endforeach; %>
</div>
</div>
</div>


