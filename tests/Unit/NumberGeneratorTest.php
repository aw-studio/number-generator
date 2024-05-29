<?php

use AwStudio\NumberGenerator\Generator;

test('It creates a new instance', function () {
    $generator = new Generator();
    expect($generator)->toBeInstanceOf(Generator::class);
});

test('It provides the next number', function () {
    $generator = new Generator();
    expect($generator->next())->toBe('1');
});

test('It provides the next number from a given start', function () {
    $generator = new Generator(
        start: 10
    );
    expect($generator->next())->toBe('11');
});

test('It provides the next number from a given start with padded zeros', function () {
    $generator = new Generator(
        start: '001',
    );
    expect($generator->next())->toBe('002');
});

test('It can be prefixed with a string', function () {
    $generator = new Generator(
        prefix: 'A'
    );
    expect($generator->next())->toBe('A1');
});

test('It can be prefixed with a string and start from a given number', function () {
    $generator = new Generator(
        start: 10,
        prefix: 'A'
    );
    expect($generator->next())->toBe('A11');
});

test('It can be suffixed with a given string', function () {
    $generator = new Generator(
        start: 10,
        suffix: 'A'
    );
    expect($generator->next())->toBe('11A');
});

test('It can be suffixed with a given string and prefixed with a string', function () {
    $generator = new Generator(
        start: 10,
        prefix: 'A',
        suffix: 'B'
    );
    expect($generator->next())->toBe('A11B');
});

test('It can generate a number from a pattern', function () {
    $generator = Generator::fromPattern('AY-1001', 'AY-{n}');
    expect($generator->next())->toBe('AY-1002');
});

test('It can generate a number from a pattern with year placeholder', function () {
    $generator = Generator::fromPattern('AY-2024001', 'AY-{Y}{n}');
    expect($generator->next())->toBe('AY-2024002');
});

test('It can generate a number from a pattern with month placeholder', function () {
    $generator = Generator::fromPattern('AY-05001', 'AY-{m}{n}');
    expect($generator->next())->toBe('AY-05002');
});

test('It can generate a number from a pattern with day placeholder', function () {
    $generator = Generator::fromPattern('AY-23001', 'AY-{d}{n}');
    expect($generator->next())->toBe('AY-23002');
});
test('It can generate a number from a pattern with dynamic date placeholder', function () {
    $generator = Generator::fromPattern('AY-23001', 'AY-{d}{n}', true);
    $day = date('d');
    expect($generator->next())->toBe('AY-'.$day.'002');
});

test('It can generate a number from a pattern with  only year placeholder', function () {
    $generator = Generator::fromPattern('2024001', '{Y}{n}');
    expect($generator->next())->toBe('2024002');
});

test('It extracts the correct number from a pattern', function () {
    $generator = (new Generator())->extractNumberByPattern('20240193', '{Y}{n}');
    expect($generator)->toBe('0193');

    $generator = (new Generator())->extractNumberByPattern('20240193', '{Y}{m}{n}');
    expect($generator)->toBe('93');
});
