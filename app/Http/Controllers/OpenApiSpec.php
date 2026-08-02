<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Task Management API',
    description: 'RESTful API for managing projects and tasks. Built with Laravel 13 + Sanctum.',
    contact: new OA\Contact(email: 'support@example.com'),
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Local development server',
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Enter the token returned from /api/v1/auth/login',
)]
#[OA\Schema(
    schema: 'UserResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id',         type: 'integer', example: 1),
        new OA\Property(property: 'name',        type: 'string',  example: 'Mohamed Hamdy'),
        new OA\Property(property: 'email',       type: 'string',  example: 'mohamed@example.com'),
        new OA\Property(property: 'created_at',  type: 'string',  format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ProjectResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id',          type: 'integer', example: 1),
        new OA\Property(property: 'name',        type: 'string',  example: 'My Project'),
        new OA\Property(property: 'description', type: 'string',  nullable: true, example: 'Project description'),
        new OA\Property(property: 'status',      type: 'string',  enum: ['active', 'completed', 'archived'], example: 'active'),
        new OA\Property(property: 'tasks_count', type: 'integer', example: 5),
        new OA\Property(property: 'created_at',  type: 'string',  format: 'date-time'),
        new OA\Property(property: 'updated_at',  type: 'string',  format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'TaskResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id',          type: 'integer', example: 1),
        new OA\Property(property: 'project_id',  type: 'integer', example: 1),
        new OA\Property(property: 'title',       type: 'string',  example: 'Fix login bug'),
        new OA\Property(property: 'description', type: 'string',  nullable: true),
        new OA\Property(property: 'priority',    type: 'string',  enum: ['low', 'medium', 'high'], example: 'high'),
        new OA\Property(property: 'status',      type: 'string',  enum: ['todo', 'in_progress', 'done'], example: 'todo'),
        new OA\Property(property: 'due_date',    type: 'string',  format: 'date', nullable: true, example: '2026-08-10'),
        new OA\Property(property: 'created_at',  type: 'string',  format: 'date-time'),
        new OA\Property(property: 'updated_at',  type: 'string',  format: 'date-time'),
    ],
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The name field is required.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
        ),
    ],
)]
class OpenApiSpec {}
