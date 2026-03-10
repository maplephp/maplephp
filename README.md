# MaplePHP Framework - Made for developers who still enjoy programming

MaplePHP is a high performance PHP framework built on PSR standards and modern best practices. It includes the core components needed for real applications such as MVC, dependency injection, routing, caching, logging, error handling, HTTP clients, and support for both web and CLI environments.

The goal is not to lock developers into a fixed ecosystem. MaplePHP gives you a robust core while letting you use the libraries and tools you prefer. You can shape the framework around your own stack and workflow, while still benefiting from updates and improvements to the core. This keeps your project flexible, maintainable, and truly yours.

Your code. Your libraries. Your framework.

## Installation

Create a new project:

```bash
composer create-project maplephp/framework my-app --stability=beta
cd my-app
````

Run the MaplePHP development server:

```bash
./maple serve
```

## Project Structure

Example structure:

```
my-app/
├── app/
│   ├── Controllers/
│   └── Services/
├── configs/
├── public/
├── routers/
├── storage/
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
