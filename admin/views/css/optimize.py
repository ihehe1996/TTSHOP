#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
CSS 优化脚本
自动优化 style.css 文件
"""

import re

def optimize_css(input_file, output_file):
    """优化CSS文件"""

    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()

    original_lines = content.count('\n') + 1
    original_size = len(content)

    print(f"原始文件: {original_lines} 行, {original_size} 字节")

    # 1. 统一主色值 #4C7D71 -> var(--admin-primary-legacy)
    # 在前1347行中替换硬编码的颜色
    lines = content.split('\n')

    # 在 :root 变量定义中添加新变量
    for i, line in enumerate(lines):
        if ':root {' in line:
            # 找到 :root 块，添加新变量
            j = i + 1
            while j < len(lines) and '}' not in lines[j]:
                j += 1
            # 在闭合括号前插入新变量
            new_vars = [
                "    --admin-primary-legacy: #4C7D71;",
                "    --admin-transition: all 0.2s ease;",
                "    --admin-radius-sm: 6px;",
                "    --admin-radius-xs: 8px;",
                "    --admin-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.08);",
                "    --admin-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);"
            ]
            lines[j:j] = new_vars
            break

    content = '\n'.join(lines)

    # 2. 替换前1347行中的硬编码颜色
    # 只替换前面部分的 #4C7D71
    parts = content.split('/* ==========================================================================')
    if len(parts) >= 2:
        # 只处理第一部分（Admin Theme 2026 之前）
        part1 = parts[0]
        part1 = part1.replace('#4C7D71', 'var(--admin-primary-legacy)')
        part1 = part1.replace('#EDF2F1', 'rgba(76, 125, 113, 0.1)')
        parts[0] = part1
        content = '/* =========================================================================='.join(parts)

    # 3. 删除明确未使用的样式
    # 删除 .template-panel.active
    content = re.sub(r'\.template-panel\.active\{[^}]+\}', '', content)

    # 删除日期选择器相关（未使用）
    content = re.sub(r'\.datetime-picker-menu[^{]*\{[^}]+\}', '', content)
    content = re.sub(r'\.date-picker-menu[^{]*\{[^}]+\}', '', content)
    content = re.sub(r'\.time-picker-menu[^{]*\{[^}]+\}', '', content)
    content = re.sub(r'\.mini-calendar[^{]*\{[^}]+\}', '', content)

    # 4. 删除多余的空行（连续3个以上空行合并为2个）
    content = re.sub(r'\n\n\n+', '\n\n', content)

    # 5. 统一 transition
    content = content.replace('transition: all 0.2s ease', 'transition: var(--admin-transition)')
    content = content.replace('transition: all 0.25s ease', 'transition: var(--admin-transition)')

    # 6. 统一 border-radius: 12px
    content = content.replace('border-radius: 12px', 'border-radius: var(--admin-radius-md)')
    content = content.replace('border-radius: 8px', 'border-radius: var(--admin-radius-xs)')
    content = content.replace('border-radius: 6px', 'border-radius: var(--admin-radius-sm)')

    # 7. 统一阴影
    content = content.replace('box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08)', 'box-shadow: var(--admin-shadow-md)')
    content = content.replace('box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08)', 'box-shadow: var(--admin-shadow-sm)')

    # 写入优化后的文件
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(content)

    optimized_lines = content.count('\n') + 1
    optimized_size = len(content)

    print(f"优化后文件: {optimized_lines} 行, {optimized_size} 字节")
    print(f"减少: {original_lines - optimized_lines} 行 ({((original_lines - optimized_lines) / original_lines * 100):.1f}%)")
    print(f"减少: {original_size - optimized_size} 字节 ({((original_size - optimized_size) / original_size * 100):.1f}%)")
    print("优化完成！")

if __name__ == '__main__':
    optimize_css('style.css', 'style_optimized.css')
