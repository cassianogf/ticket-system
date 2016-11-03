<?php

return [

/*
*  Constants
*/
'nav-settings'                  => 'Configurações',
'nav-agents'                    => 'Agentes',
'nav-dashboard'                 => 'Dashboard',
'nav-categories'                => 'Categorias',
'nav-priorities'                => 'Prioridades',
'nav-statuses'                  => 'Status',
'nav-configuration'             => 'Configuração',  
'nav-administrator'             => 'Administrator',  //new

'table-hash'                    => '#', 
'table-id'                      => 'ID',
'table-name'                    => 'Nome',
'table-action'                  => 'Função',
'table-categories'              => 'Categorias',
'table-join-category'           => 'Categorias Relacionadas',
'table-remove-agent'            => 'Remover de Agentes',
'table-remove-administrator'    => 'Remove from administrators', // New

'table-slug'                    => 'Índice', 
'table-default'                 => 'Valor Inicial', 
'table-value'                   => 'Meus Valores',  
'table-lang'                    => 'Lingua', 
'table-edit'                    => 'Editar', 

'btn-back'                      => 'Voltar',
'btn-delete'                    => 'Excluir',
'btn-edit'                      => 'Editar',
'btn-join'                      => 'Adicionar',
'btn-remove'                    => 'Remover',
'btn-submit'                    => 'Enviar',
'btn-save'                      => 'Salvar',  
'btn-update'                    => 'Atualizar',

'colon'                         => ': ',

/*
*  Page specific
*/

// tickets-admin/____
'index-title'                         => 'Sistema de Chamados - Dashboard',
'index-empty-records'                 => 'Sem Chamados ainda',
'index-total-tickets'                 => 'Total de Chamados',
'index-open-tickets'                  => 'Chamados Abertos',
'index-closed-tickets'                => 'Chamados Encerrados',
'index-performance-indicator'         => 'Indicador de Performance',
'index-periods'                       => 'Periodos',
'index-3-months'                      => '3 meses',
'index-6-months'                      => '6 meses',
'index-12-months'                     => '12 meses',
'index-tickets-share-per-category'    => 'Chamados Divididos por Categoria',
'index-tickets-share-per-agent'       => 'Chamados Divididos por Agentes',
'index-categories'                    => 'Categorias',
'index-category'                      => 'Categoria',
'index-agents'                        => 'Agentes',
'index-agent'                         => 'Agente',
'index-administrators'                => 'Administrators',  //new
'index-administrator'                 => 'Administrator',  //new
'index-users'                         => 'Usuários',
'index-user'                          => 'Usuário',
'index-tickets'                       => 'Chamados',
'index-open'                          => 'Aberto',
'index-closed'                        => 'Fechado',
'index-total'                         => 'Total',
'index-month'                         => 'Mês',
'index-performance-chart'             => 'Quantos dias na média pra resolver um chamado?',
'index-categories-chart'              => 'Chamados distribuidos por Categoria',
'index-agents-chart'                  => 'Chamados distribuidos por Agente',

// tickets-admin/agent/____
'agent-index-title'             => 'Gerenciar Agentes',
'btn-create-new-agent'          => 'Criar Novo Agente',
'agent-index-no-agents'         => 'Nenhum Agente cadastrado, ',
'agent-index-create-new'        => 'Adicionar agentes',
'agent-create-title'            => 'Adicionar Agente',
'agent-create-add-agents'       => 'Adicionar Agentes',
'agent-create-no-users'         => 'Nenhum Usuário cadastrado, Crie uma conta de Usuário Primeiro.',
'agent-create-select-user'      => 'Selecione uma conta de Usuário para criar um Agente',

// tickets-admin/administrators/____
'administrator-index-title'                   => 'Administrator Management',  //new
'btn-create-new-administrator'                => 'Create new administrator',  //new
'administrator-index-no-administrators'       => 'There are no administrators, ',  //new
'administrator-index-create-new'              => 'Add administrators',  //new
'administrator-create-title'                  => 'Add Administrator',  //new
'administrator-create-add-administrators'     => 'Add Administrators',  //new
'administrator-create-no-users'               => 'There are no user accounts, create user accounts first.',  //new
'administrator-create-select-user'            => 'Select user accounts to be added as administrators',  //new

// tickets-admin/category/____
'category-index-title'          => 'Gerenciar Categorias',
'btn-create-new-category'       => 'Criar Nova Categoria',
'category-index-no-categories'  => 'Nenhuma Categoria cadastrada, ',
'category-index-create-new'     => 'Criar Nova Categoria',
'category-index-js-delete'      => 'Você tem certeza que deseja excluir esta Categoria: ',
'category-create-title'         => 'Criar Nova Categoria',
'category-create-name'          => 'Nome',
'category-create-color'         => 'Cor',
'category-edit-title'           => 'Editar Categoria: :name',

// tickets-admin/priority/____
'priority-index-title'          => 'Gerenciar Prioridades',
'btn-create-new-priority'       => 'Criar Nova Prioridade',
'priority-index-no-priorities'  => 'Nenhuma Prioridade cadastrada, ',
'priority-index-create-new'     => 'criar nova prioridade',
'priority-index-js-delete'      => 'Você tem certeza que deseja excluir esta prioridade: ',
'priority-create-title'         => 'Criar Nova Prioridade',
'priority-create-name'          => 'Nome',
'priority-create-color'         => 'Cor',
'priority-edit-title'           => 'Editar Prioridade: :name',

// tickets-admin/status/____
'status-index-title'            => 'Gerenciar Status',
'btn-create-new-status'         => 'Criar Novo status',
'status-index-no-statuses'      => 'Nenhum Status cadastrado,',
'status-index-create-new'       => 'criar novo status',
'status-index-js-delete'        => 'Você tem certeza que deseja excluir este status: ',
'status-create-title'           => 'Criar Novo Status',
'status-create-name'            => 'Nome',
'status-create-color'           => 'Cor',
'status-edit-title'             => 'Editar Status: :name',

// tickets-admin/configuration/____
'config-index-title'            => 'Gerenciar Configurações', 
'config-index-subtitle'         => 'Configurações', 
'btn-create-new-config'         => 'Adicionar Nova Configuração', 
'config-index-no-settings'      => 'Nenhuma Configuração, ', 
'config-index-initial'          => 'Inicial', 
'config-index-tickets'          => 'Chamados', 
'config-index-notifications'    => 'Notificações', 
'config-index-permissions'      => 'Permissãos', 
'config-index-editor'           => 'Editor', //Added: 2016.01.14
'config-index-other'            => 'Outras', 
'config-create-title'           => 'Criar: Nova Configuração Global', 
'config-create-subtitle'        => 'Criar Configuração', 
'config-edit-title'             => 'Editar: Configuração Global', 
'config-edit-subtitle'          => 'Editar Configuração', 
'config-edit-id'                => 'ID',
'config-edit-slug'              => 'Índice',
'config-edit-default'           => 'Valor Inicial',
'config-edit-value'             => 'Meu Valor',
'config-edit-language'          => 'Lingua',
'config-edit-unserialize'       => 'Get the array values, and change the values',
'config-edit-serialize'         => 'Get the serialized string of the changed values (to be entered in the field)',
'config-edit-should-serialize'  => 'Serialize', //Added: 2016-01-16
'config-edit-eval-warning'      => 'When checked, the server will run eval()!
                                      Don\'t use this if eval() is disabled on your server or if you don\'t exactly know what you are doing!
                                      Exact code executed:', //Added: 2016-01-16
'config-edit-reenter-password'  => 'Re-enter your password', //Added: 2016-01-16
'config-edit-auth-failed'       => 'Password mismatch', //Added: 2016-01-16
'config-edit-eval-error'        => 'Invalid value', //Added: 2016-01-16
'config-edit-tools'             => 'Tools:',
];
