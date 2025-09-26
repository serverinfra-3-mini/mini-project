# Docker Swarm 클러스터 자동 설정 가이드

이 문서는 Ansible을 사용하여 Docker Swarm 클러스터를 자동으로 설정하는 방법을 안내합니다. 제공된 스크립트와 플레이북을 통해 Git 클론부터 클러스터 확인까지 모든 과정을 손쉽게 수행할 수 있습니다.

## 1. 전제 조건

*   **Ansible 컨트롤 노드**: 이 스크립트를 실행할 Linux 기반의 시스템 (Rocky Linux 환경 가정).
*   **대상 서버 (Master 및 Worker)**: Rocky Linux 9 환경으로 가정합니다.
*   **네트워크 접근**:
    *   컨트롤 노드에서 대상 서버들로 SSH(기본 22번 포트) 접속이 가능해야 합니다.
    *   Docker Swarm 통신을 위해 다음 포트들이 열려 있어야 합니다 (플레이북에서 `firewalld`를 통해 자동으로 설정).
        *   `2377/tcp` (Swarm 관리 통신)
        *   `7946/tcp`, `7946/udp` (Overlay 네트워크 노드 발견)
        *   `4789/udp` (Overlay 네트워크 VXLAN 트래픽)
*   **대상 서버 SSH 접속**: 대상 서버들의 `root` 계정으로 SSH 비밀번호를 사용하여 접속이 가능해야 합니다. (`ansible.cfg`의 `remote_user=root` 설정 가정)

## 2. 프로젝트 저장소 클론 (Clone the Repository)

Ansible 컨트롤 노드에서 다음 명령어를 사용하여 프로젝트 저장소를 클론합니다.

```bash
git clone https://github.com/serverinfra-3-mini/mini-project.git
```

## 3. 디렉토리 이동 (Change Directory)
클론한 저장소 디렉토리로 이동합니다.

```bash
cd mini-project
```

## 4. inventory.ini 파일 수정 (Modify Inventory File)
inventory.ini 파일을 열어 Docker Swarm 마스터 노드와 워커 노드로 사용할 서버들의 실제 IP 주소를 반영합니다.

```ini
# inventory.ini
[master]
192.168.100.132  # <-- 마스터 노드의 실제 IP 주소로 수정

[worker]
192.168.100.133  # <-- 첫 번째 워커 노드의 실제 IP 주소로 수정
192.168.100.135  # <-- 두 번째 워커 노드의 실제 IP 주소로 수정
192.168.100.136  # <-- 세 번째 워커 노드의 실제 IP 주소로 수정

[all:vars]
ansible_python_interpreter=/usr/bin/python3
```
> 텍스트 에디터(예: vi, nano)를 사용하여 파일을 수정할 수 있습니다:

`vi inventory.ini`

## 5. 설치 스크립트 실행 (Run the Installation Script)
모든 설정이 완료되었다면, init.sh 스크립트를 실행하여 Ansible 환경 설정 및 Docker Swarm 배포를 시작합니다.

1. **5.1. 스크립트 실행**
    ```bash
    source ./init.sh
    ```

2. **5.2. SSH 비밀번호 입력 안내**

    스크립트 실행 중 비밀번호 입력을 요구할 수 있습니다. 프롬프트에 정확하게 응답해야 합니다.

    초기 비밀번호 입력: 스크립트 실행 시 필수 패키지 설치 이후 다음과 같은 프롬프트가 나타납니다.

    이때, 모든 대상 노드(마스터 및 워커)의 root 계정 비밀번호를 입력하고 엔터를 누릅니다.

    `SSH password:`

    root 계정의 비밀번호를 정확히 입력하고 엔터를 눌러주세요.

    이 단계가 완료되면 컨트롤 노드의 SSH 키가 원격 노드에 성공적으로 배포되어, 이후 플레이북들은 비밀번호 입력 없이 SSH 키 인증을 통해 자동으로 접속하게 됩니다.

## 6. 클러스터 확인 (Verify the Cluster)
모든 플레이북 실행이 성공적으로 완료되었다면, 마스터 노드에 SSH로 접속하여 Docker Swarm 클러스터 상태를 확인합니다.

마스터 노드에 SSH 접속:

`ssh root@<마스터_노드_IP>`

(예: `ssh root@192.168.100.132`)

Docker Swarm 노드 목록 확인:

`docker node ls`


예상 출력 결과: 마스터 노드가 Leader로, 모든 워커 노드가 Ready 상태로 나타나야 합니다.
```
ID                            HOSTNAME            STATUS    AVAILABILITY   MANAGER STATUS   ENGINE VERSION
1a2b3c4d5e...                 node-master         Ready     Active         Leader           24.0.5
6f7g8h9i0j...                 node-worker1        Ready     Active         <none>           24.0.5
k1l2m3n4o5...                 node-worker2        Ready     Active         <none>           24.0.5
...
```
