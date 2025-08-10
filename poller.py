#!/usr/bin/env python3
# poller.py - scan services.json and update status.json with timestamp
import json, subprocess, datetime, os

BASE = os.path.dirname(__file__)
SERVICES_FILE = '/var/www/html/service-monitor/services.json'
STATUS_FILE = '/var/www/html/service-monitor/status.json'

def get_status(svc):
    try:
        out = subprocess.check_output(['/bin/systemctl','is-active',svc], stderr=subprocess.STDOUT, text=True).strip()
        return out
    except subprocess.CalledProcessError as e:
        return 'failed'

def main():
    with open(SERVICES_FILE) as f:
        services = json.load(f)['services']
    data = {}
    now = datetime.datetime.utcnow().isoformat() + 'Z'
    for svc in services.keys():
        data[svc] = {'status': get_status(svc), 'last_checked': now}
    with open(STATUS_FILE + '.tmp','w') as t:
        json.dump(data, t, indent=2)
    os.replace(STATUS_FILE + '.tmp', STATUS_FILE)

if __name__ == '__main__':
    main()

