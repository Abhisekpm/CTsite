#!/bin/bash

# Custom Order Test Runner Script
# This script runs comprehensive tests for the custom cake order functionality

echo "🎂 Custom Cake Order Test Suite"
echo "================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    print_error "This script must be run from the root of a Laravel project"
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    print_error "Vendor directory not found. Please run 'composer install' first."
    exit 1
fi

# Function to run tests with error handling
run_test() {
    local test_name="$1"
    local test_command="$2"
    
    print_status "Running $test_name..."
    
    if eval $test_command; then
        print_success "$test_name completed successfully"
        return 0
    else
        print_error "$test_name failed"
        return 1
    fi
}

# Check for test type argument
TEST_TYPE=${1:-"all"}

case $TEST_TYPE in
    "all")
        echo "Running all custom order tests..."
        ;;
    "feature")
        echo "Running only feature tests..."
        ;;
    "unit")
        echo "Running only unit tests..."
        ;;
    "performance")
        echo "Running only performance tests..."
        ;;
    "quick")
        echo "Running quick test suite (excluding performance tests)..."
        ;;
    *)
        echo "Usage: $0 [all|feature|unit|performance|quick]"
        echo ""
        echo "Options:"
        echo "  all         - Run all tests (default)"
        echo "  feature     - Run only feature tests"
        echo "  unit        - Run only unit tests" 
        echo "  performance - Run only performance tests"
        echo "  quick       - Run quick tests (exclude performance)"
        exit 1
        ;;
esac

echo ""

# Prepare test environment
print_status "Preparing test environment..."

# Run database preparation with explicit SQLite configuration
if ! run_test "Database preparation" "php artisan migrate:fresh --database=testing --force"; then
    echo "❌ Database preparation failed!"
    exit 1
fi

echo "🔄 Setting up test database with SQLite in memory..."

echo "🌱 Running seeders for test data..."
if ! run_test "Database seeding" "php artisan db:seed --database=testing --force"; then
    echo "⚠️  Warning: Some seeders failed, but continuing with tests..."
fi

# Start test execution
failed_tests=0
total_tests=0

if [ "$TEST_TYPE" = "all" ] || [ "$TEST_TYPE" = "feature" ] || [ "$TEST_TYPE" = "quick" ]; then
    echo ""
    echo "🔍 Feature Tests"
    echo "==============="
    
    # Custom Order Submission Tests
    ((total_tests++))
    if ! run_test "Custom Order Submission Tests" "vendor/bin/phpunit tests/Feature/CustomOrderSubmissionTest.php --verbose"; then
        ((failed_tests++))
    fi
    
    # Admin Order Management Tests
    ((total_tests++))
    if ! run_test "Admin Order Management Tests" "vendor/bin/phpunit tests/Feature/AdminOrderManagementTest.php --verbose"; then
        ((failed_tests++))
    fi
fi

if [ "$TEST_TYPE" = "all" ] || [ "$TEST_TYPE" = "unit" ] || [ "$TEST_TYPE" = "quick" ]; then
    echo ""
    echo "🧪 Unit Tests"
    echo "============="
    
    # Order Controller Unit Tests
    ((total_tests++))
    if ! run_test "Order Controller Unit Tests" "vendor/bin/phpunit tests/Unit/OrderControllerTest.php --verbose"; then
        ((failed_tests++))
    fi
fi

if [ "$TEST_TYPE" = "all" ] || [ "$TEST_TYPE" = "performance" ]; then
    echo ""
    echo "⚡ Performance Tests"
    echo "==================="
    
    print_warning "Performance tests may take longer to complete..."
    
    # Performance Tests
    ((total_tests++))
    if ! run_test "Order Performance Tests" "vendor/bin/phpunit tests/Feature/OrderPerformanceTest.php --verbose"; then
        ((failed_tests++))
    fi
fi

# Additional test configurations
if [ "$TEST_TYPE" = "all" ]; then
    echo ""
    echo "🔧 Additional Test Configurations"
    echo "================================="
    
    # Test with coverage (if xdebug is enabled)
    if php -m | grep -q xdebug; then
        print_status "Running tests with coverage report..."
        ((total_tests++))
        if ! run_test "Coverage Report Generation" "vendor/bin/phpunit tests/Feature/CustomOrderSubmissionTest.php --coverage-text --coverage-filter=app/Http/Controllers/OrderController.php"; then
            ((failed_tests++))
        fi
    else
        print_warning "Xdebug not enabled. Skipping coverage tests."
    fi
fi

# Test Summary
echo ""
echo "📊 Test Summary"
echo "==============="

if [ $failed_tests -eq 0 ]; then
    print_success "All $total_tests test suite(s) passed! 🎉"
    echo ""
    echo "Your custom cake order functionality is working correctly!"
    echo ""
    echo "Next steps:"
    echo "1. Deploy to staging environment"
    echo "2. Run manual testing scenarios"
    echo "3. Monitor performance in production"
    exit 0
else
    print_error "$failed_tests out of $total_tests test suite(s) failed."
    echo ""
    echo "Please review the failed tests above and fix any issues."
    echo ""
    echo "Common fixes:"
    echo "1. Check database migrations are up to date"
    echo "2. Verify environment configuration"
    echo "3. Ensure all dependencies are installed"
    echo "4. Check file permissions for storage directory"
    exit 1
fi

# Helper functions for development
if [ "$2" = "--help-dev" ]; then
    echo ""
    echo "🛠️  Development Helper Commands"
    echo "==============================="
    echo ""
    echo "Generate test data:"
    echo "  php artisan tinker"
    echo "  >>> CustomOrder::factory()->count(10)->create()"
    echo ""
    echo "Run specific test methods:"
    echo "  vendor/bin/phpunit --filter=test_successful_order_submission_without_images"
    echo ""
    echo "Debug failing tests:"
    echo "  vendor/bin/phpunit tests/Feature/CustomOrderSubmissionTest.php --debug"
    echo ""
    echo "Clear test artifacts:"
    echo "  php artisan cache:clear --env=testing"
    echo "  php artisan config:clear --env=testing"
    echo ""
fi 