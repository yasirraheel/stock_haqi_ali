# Contributing to Stock Haqi Ali

Thank you for your interest in contributing to Stock Haqi Ali! This document provides guidelines and information for contributors.

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## How to Contribute

### Reporting Issues

1. **Bug Reports**: Use the GitHub issue tracker to report bugs
2. **Feature Requests**: Suggest new features through issues
3. **Security Issues**: Report security vulnerabilities privately to the maintainers

### Making Changes

1. **Fork the Repository**: Create your own fork of the project
2. **Create a Branch**: Create a feature branch for your changes
3. **Make Changes**: Implement your changes following the coding standards
4. **Test Your Changes**: Ensure all tests pass and new functionality is tested
5. **Submit a Pull Request**: Create a pull request with a clear description

### Development Setup

1. **Clone Your Fork**:
   ```bash
   git clone https://github.com/your-username/stock_haqi_ali.git
   cd stock_haqi_ali
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Set Up Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set Up Database**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Coding Standards

### PHP/Laravel
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Write unit tests for new features
- Follow Laravel conventions

### JavaScript
- Use ES6+ features
- Follow consistent indentation (2 spaces)
- Use meaningful variable names
- Add JSDoc comments for functions

### CSS
- Use consistent indentation (2 spaces)
- Follow BEM methodology for class naming
- Use meaningful class names
- Organize styles logically

## Pull Request Guidelines

### Before Submitting
- [ ] Code follows the project's coding standards
- [ ] All tests pass
- [ ] New features have appropriate tests
- [ ] Documentation is updated if needed
- [ ] No merge conflicts

### Pull Request Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tests pass locally
- [ ] New tests added for new functionality
- [ ] Manual testing completed

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No breaking changes (or documented)
```

## Development Guidelines

### Database Changes
- Always create migrations for database changes
- Include rollback functionality in migrations
- Test migrations on sample data

### API Changes
- Maintain backward compatibility when possible
- Document API changes
- Update API documentation

### Security
- Validate all inputs
- Use prepared statements for database queries
- Implement proper authentication and authorization
- Follow Laravel security best practices

## Testing

### Running Tests
```bash
# Run PHP tests
php artisan test

# Run specific test
php artisan test --filter TestName

# Run with coverage
php artisan test --coverage
```

### Writing Tests
- Write unit tests for business logic
- Write feature tests for API endpoints
- Write browser tests for user interactions
- Aim for high test coverage

## Documentation

### Code Documentation
- Document complex functions and classes
- Use PHPDoc for PHP functions
- Use JSDoc for JavaScript functions
- Keep documentation up to date

### User Documentation
- Update README.md for new features
- Document configuration changes
- Provide examples for new functionality

## Release Process

### Version Numbering
- Follow semantic versioning (MAJOR.MINOR.PATCH)
- Update version numbers in composer.json and package.json
- Create release notes for each version

### Release Checklist
- [ ] All tests pass
- [ ] Documentation updated
- [ ] Version numbers updated
- [ ] Release notes created
- [ ] Changelog updated

## Getting Help

### Resources
- Laravel Documentation: https://laravel.com/docs
- PHP Documentation: https://www.php.net/docs.php
- GitHub Issues: Use the issue tracker for questions

### Community
- Join discussions in GitHub issues
- Ask questions in the issue tracker
- Share your experience with the project

## Recognition

Contributors will be recognized in:
- CONTRIBUTORS.md file
- Release notes
- Project documentation

Thank you for contributing to Stock Haqi Ali!
