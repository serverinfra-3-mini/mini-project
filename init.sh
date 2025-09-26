#!/bin/bash

# 에러 발생 시 스크립트 즉시 종료
set -e


echo "Ansible 설치를 시작합니다..."

# 1. 시스템 업데이트 및 Ansible 설치

# epel-release가 설치되어 있지 않으면 설치
if ! sudo dnf list installed epel-release &>/dev/null; then
    echo "epel-release를 설치합니다."
    sudo dnf install -y epel-release
fi

# Ansible이 설치되어 있지 않으면 설치
if ! sudo dnf list installed ansible &>/dev/null; then
    echo "Ansible을 설치합니다."
    sudo dnf install -y ansible
else
    echo "Ansible은 이미 설치되어 있습니다."
fi

echo "Ansible 설치가 완료되었습니다."


# 2. Ansible 플레이북 실행
echo "플레이북 실행을 시작합니다..."

# --ask-pass (-k) 옵션이 있으면 Ansible은 표준 입력에서 SSH 비밀번호를 읽습니다.

ansible-playbook playbook.yml -k

echo "플레이북 실행이 완료되었습니다!"

