# MaplePHP CLI App

A lightweight CLI application skeleton built on the MaplePHP ecosystem. It provides a modern command-line framework with routing, controllers, dependency injection, services, and PSR-compatible components so you can build any CLI application or automation tool.

This project is intended to be installed using Composer’s `create-project` and serves as the starting point for CLI applications.

## Installation

Create a new project:

```bash
composer create-project maplephp/cli-app my-app
cd my-app
````

Run the CLI:

```bash
./cli
```

## Usage

Typical commands:

```bash
./cli list
./cli run --arg=1
./cli help
```


## Project Structure

Example structure:

```
my-app/
├── app/
│   ├── Controllers/
│   └── Services/
├── routers/
├── vendor/
```

## Features

* Command routing and dispatch
* Controller and service structure
* Dependency Injection container
* PSR-compatible components
* Command helpers and tooling
* Extendable architecture for plugins or modules

## Relationship to Unitary

`maplephp/unitary` is the testing framework and provides:

```
vendor/bin/unitary
```
