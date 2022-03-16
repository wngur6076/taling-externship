#!/bin/bash

# git_pull_and_rerun.sh 파일내용

# 테스트 환경에서 테스트 먼저
{
  docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling-test/backend/ ; git pull origin master"
  docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling-test/backend/ ; composer install"
  docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling-test/backend/ ; php artisan migrate"
  docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling-test/backend/ ; php artisan test"
} || {
  exit 1
}

# 폴더에 깃에 있는 최신소스코드 가져오기
docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling/backend/ ; git pull origin master"

# 의존성 설치
docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling/backend/ ; composer install"

# 마이그레이트
docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling/backend/ ; php artisan migrate --force"

# 스웨거 생성
docker exec php__1 bash -ce "cd /data/site_projects/php__1/site_projects/laravel-taling/backend/ ; php artisan l5-swagger:generate"
exit 0
