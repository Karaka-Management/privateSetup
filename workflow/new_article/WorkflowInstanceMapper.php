<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Workflow
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Workflow\Models;

use Modules\Admin\Models\AccountMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use Modules\Workflow\Models\WorkflowTemplateMapper;

require_once __DIR__ . '/WorkflowInstance.php';

/**
 * Mapper class.
 *
 * @package Modules\Workflow
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class WorkflowInstanceMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'workflow_{workflow_id}_instance_id'         => ['name' => 'workflow_{workflow_id}_instance_id',         'type' => 'int',      'internal' => 'id'],
        'workflow_{workflow_id}_instance_title'      => ['name' => 'workflow_{workflow_id}_instance_title',      'type' => 'string',   'internal' => 'title'],
        'workflow_{workflow_id}_instance_template'   => ['name' => 'workflow_{workflow_id}_instance_template',   'type' => 'int',      'internal' => 'createdBy', 'readonly' => true],
        'workflow_{workflow_id}_instance_created_by' => ['name' => 'workflow_{workflow_id}_instance_created_by', 'type' => 'int',      'internal' => 'createdBy', 'readonly' => true],
        'workflow_{workflow_id}_instance_created_at' => ['name' => 'workflow_{workflow_id}_instance_created_at', 'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:string, external:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'template' => [
            'mapper'     => WorkflowTemplateMapper::class,
            'external'   => 'workflow_{workflow_id}_instance_template',
        ],
        'createdBy' => [
            'mapper'     => AccountMapper::class,
            'external'   => 'workflow_{workflow_id}_instance_created_by',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'workflow_{workflow_id}_instance';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'workflow_{workflow_id}_instance_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='workflow_{workflow_id}_instance_id';

    /**
     * Model.
     *
     * @var string
     * @since 1.0.0
     */
    public const MODEL = WorkflowInstance::class;
}
