# Flexplorer Helm Chart

This Helm chart deploys Flexplorer, an ABRA Flexi API developer tool, on a Kubernetes cluster.

## Prerequisites

- Kubernetes 1.19+
- Helm 3.0+

## Installation

### Install from local chart

```bash
helm install flexplorer ./helm/flexplorer
```

### Install with custom values

```bash
helm install flexplorer ./helm/flexplorer \
  --set flexplorer.flexibeeUrl=https://your-flexibee-server.com \
  --set flexplorer.flexibeeLogin=your-username \
  --set flexplorer.flexibeePassword=your-password \
  --set flexplorer.flexibeeCompany=your-company
```

### Install with ingress enabled

```bash
helm install flexplorer ./helm/flexplorer \
  --set ingress.enabled=true \
  --set ingress.hosts[0].host=flexplorer.example.com \
  --set ingress.hosts[0].paths[0].path=/ \
  --set ingress.hosts[0].paths[0].pathType=Prefix
```

### Install with persistence enabled

```bash
helm install flexplorer ./helm/flexplorer \
  --set persistence.enabled=true \
  --set persistence.size=5Gi
```

## Configuration

The following table lists the configurable parameters and their default values.

| Parameter | Description | Default |
|-----------|-------------|---------|
| `replicaCount` | Number of replicas | `1` |
| `image.repository` | Image repository | `vitexsoftware/flexplorer` |
| `image.tag` | Image tag | `latest` |
| `image.pullPolicy` | Image pull policy | `IfNotPresent` |
| `service.type` | Service type | `ClusterIP` |
| `service.port` | Service port | `80` |
| `ingress.enabled` | Enable ingress | `false` |
| `ingress.className` | Ingress class name | `""` |
| `ingress.hosts` | Ingress hosts configuration | `[]` |
| `flexplorer.flexibeeUrl` | ABRA Flexi URL | `https://demo.flexibee.eu` |
| `flexplorer.flexibeeLogin` | ABRA Flexi login | `winstrom` |
| `flexplorer.flexibeePassword` | ABRA Flexi password | `winstrom` |
| `flexplorer.flexibeeCompany` | ABRA Flexi company | `demo` |
| `flexplorer.backupDirectory` | Backup directory path | `/var/tmp` |
| `persistence.enabled` | Enable persistence | `false` |
| `persistence.size` | PVC size | `1Gi` |
| `persistence.storageClass` | Storage class | `""` |
| `resources` | CPU/Memory resource requests/limits | `{}` |
| `autoscaling.enabled` | Enable HPA | `false` |

## Uninstall

```bash
helm uninstall flexplorer
```

## Upgrading

```bash
helm upgrade flexplorer ./helm/flexplorer
```

## Support

For issues and questions, please visit: https://github.com/VitexSoftware/Flexplorer
