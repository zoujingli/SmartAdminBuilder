# SmartAdminBuilder

[![Latest Stable Version](https://img.shields.io/packagist/v/zoujingli/smart-admin-builder.svg)](https://packagist.org/packages/zoujingli/smart-admin-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/zoujingli/smart-admin-builder.svg)](https://packagist.org/packages/zoujingli/smart-admin-builder)
[![Monthly Downloads](https://img.shields.io/packagist/dm/zoujingli/smart-admin-builder.svg)](https://packagist.org/packages/zoujingli/smart-admin-builder)
[![License](https://img.shields.io/packagist/l/zoujingli/smart-admin-builder.svg)](https://packagist.org/packages/zoujingli/smart-admin-builder)

SmartAdminBuilder 是 SmartAdmin 生态的开源构建器 Composer 包，提供 Phar/SFX 打包、配置 AST 改写、源码加固、前端资源归档和二进制产物生成能力。

## 仓库定位

| 项目 | 说明 |
|------|------|
| 仓库 | [`zoujingli/SmartAdminBuilder`](https://github.com/zoujingli/SmartAdminBuilder) |
| 可见性 | Public / Apache-2.0 开源 |
| Composer 包 | `zoujingli/smart-admin-builder` |
| 面向对象 | SmartAdmin 发布构建流程、私有化交付流水线和需要复用构建能力的二次开发项目 |

## 能力范围

- Hyperf 项目 Phar/SFX 打包入口与 `xadmin:build:phar` 构建命令。
- 配置 AST 改写、生产配置收敛和 Phar 流路径适配。
- 一方源码压缩、字符串处理、源码加固运行时和构建产物清理。
- 前端 `web/dist` 归档为 `dist.zip`，并与 SmartAdmin 二进制构建流程衔接。

## Installation

```bash
composer require zoujingli/smart-admin-builder
```

## License

Apache License 2.0。
