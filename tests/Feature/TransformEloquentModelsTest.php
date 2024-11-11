<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nolanos\LaravelModelTypescriptTransformer\ModelTransformer;

afterEach(function () {

    if (Schema::hasTable('test_table')) {
        Schema::drop('test_table');
    }
});

test('returns null for non model', function () {
    $non_model = new class {
    };
    $transformer = new ModelTransformer;
    $reflectionClass = new \ReflectionClass($non_model::class);
    expect($transformer->transform($reflectionClass, $non_model::class))->toBeNull();
});

test('it maps types properly', function () {
    if (Schema::hasTable('test_table')) {
        Schema::drop('test_table');
    }

    Schema::create('test_table', function (Blueprint $table) {
        // Numbers
        $table->id();
        $table->integer('an_integer');
        $table->bigInteger('a_big_integer');
        $table->unsignedBigInteger('unsigned_big_integer');
        $table->unsignedInteger('unsigned_integer');
        $table->unsignedMediumInteger('unsigned_medium_integer');
        $table->unsignedSmallInteger('unsigned_small_integer');
        $table->unsignedTinyInteger('unsigned_tiny_integer');
        $table->double('a_double');
        $table->decimal('a_decimal');
        // String
        $table->uuid();
        $table->string('a_string');
        $table->text('a_text');
        $table->char('a_char');
        $table->date('a_date');
        $table->dateTime('a_datetime');
        $table->timestamp('a_timestamp');
        $table->timestampsTz();
        $table->time('a_time');
        $table->foreignId('user_id');
        $table->binary('a_binary');
        $table->enum('an_enum', ['a', 'b', 'c']);
        // Boolean
//        $table->boolean('a_boolean');
    });

    $model = new class extends \Illuminate\Database\Eloquent\Model {
        protected $table = 'test_table';
    };

    $transformer = new ModelTransformer;
    $transformedType = $transformer->transform(new \ReflectionClass($model::class), $model::class);

    $type_definition = "{\n" .
        // Numbers
        "id: number\n" .
        "an_integer: number\n" .
        "a_big_integer: number\n" .
        "unsigned_big_integer: number\n" .
        "unsigned_integer: number\n" .
        "unsigned_medium_integer: number\n" .
        "unsigned_small_integer: number\n" .
        "unsigned_tiny_integer: number\n" .
        "a_double: number\n" .
        "a_decimal: number\n" .
        // String
        "uuid: string\n" .
        "a_string: string\n" .
        "a_text: string\n" .
        "a_char: string\n" .
        "a_date: string\n" .
        "a_datetime: string\n" .
        "a_timestamp: string\n" .
        "created_at: string | null\n" .
        "updated_at: string | null\n" .
        "a_time: string\n" .
        "user_id: number\n" .
        "a_binary: string\n" .
        "an_enum: string\n" .
        // Boolean
//        "a_boolean: boolean\n".
        '}';

    expect($transformedType?->transformed)->toEqual($type_definition);
});

test('it respects casts properly', function () {
    if (Schema::hasTable('test_table')) {
        Schema::drop('test_table');
    }

    Schema::create('test_table', function (Blueprint $table) {
        $table->id();
        $table->boolean('a_tiny_int'); //
        $table->json('some_json');
    });

    $model = new class extends \Illuminate\Database\Eloquent\Model {
        protected $table = 'test_table';
        protected $casts = [
            'a_tiny_int' => 'boolean',
            'some_json' => 'array',
        ];
    };

    $transformer = new ModelTransformer;
    $transformedType = $transformer->transform(new \ReflectionClass($model::class), $model::class);

    $type_definition = "{\n" .
        // Numbers
        "id: number\n" .
        // Boolean
        "a_tiny_int: boolean\n" .
        // Array
        "some_json: any\n" .
        '}';

    expect($transformedType?->transformed)->toEqual($type_definition);
});


test('it adds appended attributes', function () {
    if (Schema::hasTable('test_table')) {
        Schema::drop('test_table');
    }

    Schema::create('test_table', function (Blueprint $table) {
        $table->id();
    });

    $model = new class extends \Illuminate\Database\Eloquent\Model {
        protected $table = 'test_table';
        protected $appends = ['appended_attribute'];
     
        public function appendedAttribute()
        {
            new Attribute(get: fn() => 'some value');
        }
    };

    $transformer = new ModelTransformer;
    $transformedType = $transformer->transform(new \ReflectionClass($model::class), $model::class);

    $type_definition = "{\n" .
        // Numbers
        "id: number\n" .
        "appended_attribute: any\n" .
        '}';

    expect($transformedType?->transformed)->toEqual($type_definition);
});